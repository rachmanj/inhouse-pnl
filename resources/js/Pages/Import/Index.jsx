import { Head, Link, router } from '@inertiajs/react';
import { ProTable } from '@ant-design/pro-components';
import { Button, Space, Typography } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import StatusChip from '@/Components/Shared/StatusChip';
import { usePermission } from '@/Hooks/usePermission';

export default function Index({ batches = [] }) {
    const { can } = usePermission();

    const columns = [
        { title: 'ID', dataIndex: 'id', width: 70 },
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
            render: (status) => <StatusChip status={status} />,
        },
        { title: 'File', dataIndex: 'original_filename', ellipsis: true },
        {
            title: 'Rows',
            render: (_, record) => (
                <Typography.Text type="secondary">
                    {record.staged_rows ?? 0}/{record.total_rows ?? 0}
                    {(record.error_rows ?? 0) > 0 && (
                        <Typography.Text type="danger"> ({record.error_rows} err)</Typography.Text>
                    )}
                </Typography.Text>
            ),
        },
        {
            title: 'Triggered By',
            render: (_, record) => record.triggered_by_user?.name ?? record.triggered_by?.name ?? '—',
        },
        {
            title: 'Started',
            dataIndex: 'started_at',
            valueType: 'dateTime',
        },
        {
            title: 'Actions',
            key: 'actions',
            render: (_, record) => (
                <Space>
                    <Link href={route('imports.show', record.id)}>
                        <Button size="small">View</Button>
                    </Link>
                    {can('imports.manage') && ['pending', 'failed'].includes(record.status) && (
                        <Button
                            size="small"
                            danger
                            onClick={() => router.delete(route('imports.destroy', record.id))}
                        >
                            Cancel
                        </Button>
                    )}
                </Space>
            ),
        },
    ];

    return (
        <AuthenticatedLayout title="Import Center">
            <Head title="Import Center" />
            {can('imports.create') && (
                <Link href={route('imports.create')}>
                    <Button type="primary" style={{ marginBottom: 16 }}>
                        New Import
                    </Button>
                </Link>
            )}
            <ProTable
                rowKey="id"
                search={false}
                options={false}
                columns={columns}
                dataSource={batches}
                pagination={{ pageSize: 20 }}
            />
        </AuthenticatedLayout>
    );
}
