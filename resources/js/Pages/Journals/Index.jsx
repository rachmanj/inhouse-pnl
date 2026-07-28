import { Head, Link, router } from '@inertiajs/react';
import { ProTable } from '@ant-design/pro-components';
import { Button, Space } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import JournalBalanceBadge from '@/Components/Journals/JournalBalanceBadge';
import StatusChip from '@/Components/Shared/StatusChip';
import { usePermission } from '@/Hooks/usePermission';

export default function Index({ journals = [] }) {
    const { can } = usePermission();

    const columns = [
        { title: 'Reference', dataIndex: 'reference_no', render: (v, record) => (
            <Link href={route('journals.show', record.id)}>{v}</Link>
        ) },
        {
            title: 'Period',
            render: (_, record) => {
                const p = record.report_period ?? record.period;
                return p ? `${p.year}-${String(p.month).padStart(2, '0')}` : '—';
            },
        },
        {
            title: 'Site',
            render: (_, record) => record.project_site?.code ?? record.site?.code ?? '—',
        },
        { title: 'Source', dataIndex: 'source' },
        {
            title: 'Status',
            dataIndex: 'status',
            render: (s) => <StatusChip status={s} />,
        },
        {
            title: 'Balance',
            render: (_, record) => (
                <JournalBalanceBadge
                    totalDebit={record.total_debit}
                    totalCredit={record.total_credit}
                />
            ),
        },
        { title: 'Description', dataIndex: 'description', ellipsis: true },
        {
            title: 'Actions',
            key: 'actions',
            render: (_, record) => (
                <Space>
                    <Link href={route('journals.show', record.id)}>
                        <Button size="small">View</Button>
                    </Link>
                </Space>
            ),
        },
    ];

    return (
        <AuthenticatedLayout title="Journals">
            <Head title="Journals" />
            {can('journals.manage') && (
                <Link href={route('journals.create')}>
                    <Button type="primary" style={{ marginBottom: 16 }}>
                        New Journal
                    </Button>
                </Link>
            )}
            <ProTable
                rowKey="id"
                search={false}
                options={false}
                columns={columns}
                dataSource={journals}
                pagination={{ pageSize: 20 }}
            />
        </AuthenticatedLayout>
    );
}
