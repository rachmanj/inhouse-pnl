import { Head, router } from '@inertiajs/react';
import { ProDescriptions } from '@ant-design/pro-components';
import { Button, Input, Popconfirm, Space } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import JournalBalanceBadge from '@/Components/Journals/JournalBalanceBadge';
import JournalLineTable from '@/Components/Journals/JournalLineTable';
import StatusChip from '@/Components/Shared/StatusChip';
import { usePermission } from '@/Hooks/usePermission';

export default function Show({ journal, lines = [] }) {
    const { can } = usePermission();
    const totalDebit = lines.reduce((s, l) => s + Number(l.debit || 0), 0);
    const totalCredit = lines.reduce((s, l) => s + Number(l.credit || 0), 0);

    const approve = () => router.post(route('journals.approve', journal.id));
    const reject = (comments) => router.post(route('journals.reject', journal.id), { comments });

    return (
        <AuthenticatedLayout title={`Journal ${journal.reference_no}`}>
            <Head title={journal.reference_no} />

            <ProDescriptions
                column={3}
                style={{ marginBottom: 16 }}
                dataSource={journal}
                columns={[
                    { title: 'Reference', dataIndex: 'reference_no' },
                    {
                        title: 'Period',
                        render: () => {
                            const p = journal.report_period ?? journal.period;
                            return p ? `${p.year}-${String(p.month).padStart(2, '0')}` : '—';
                        },
                    },
                    {
                        title: 'Site',
                        render: () => journal.project_site?.code ?? journal.site?.code ?? '—',
                    },
                    { title: 'Source', dataIndex: 'source' },
                    {
                        title: 'Status',
                        dataIndex: 'status',
                        render: (s) => <StatusChip status={s} />,
                    },
                    { title: 'Description', dataIndex: 'description', span: 2 },
                ]}
            />

            <div style={{ marginBottom: 16 }}>
                <JournalBalanceBadge totalDebit={totalDebit} totalCredit={totalCredit} />
            </div>

            <JournalLineTable lines={lines} />

            {can('journals.approve') && journal.status === 'pending_approval' && (
                <Space style={{ marginTop: 24 }}>
                    <Button type="primary" onClick={approve}>
                        Approve
                    </Button>
                    <Popconfirm
                        title="Reject journal"
                        description={(
                            <Input.TextArea
                                id="reject-comments"
                                placeholder="Comments required"
                                rows={3}
                            />
                        )}
                        onConfirm={() => {
                            const el = document.getElementById('reject-comments');
                            reject(el?.value);
                        }}
                    >
                        <Button danger>Reject</Button>
                    </Popconfirm>
                </Space>
            )}
        </AuthenticatedLayout>
    );
}
