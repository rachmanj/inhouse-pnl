import { Head, Link, router } from '@inertiajs/react';
import { ProTable } from '@ant-design/pro-components';
import { Button, Popconfirm, Space } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Index({ mappings }) {
    const columns = [
        {
            title: 'Account',
            key: 'account',
            render: (_, record) => record.account?.sap_code ?? '-',
        },
        {
            title: 'P&L Line',
            key: 'pnl_line',
            render: (_, record) => record.pnl_line?.code ?? '-',
        },
        { title: 'Effective From', dataIndex: 'effective_from', key: 'effective_from' },
        { title: 'Version', dataIndex: 'version', key: 'version' },
        {
            title: 'Actions',
            key: 'actions',
            render: (_, record) => (
                <Space>
                    <Link href={route('admin.coa-mappings.edit', record.id)}>
                        <Button size="small">Edit</Button>
                    </Link>
                    <Popconfirm
                        title="Delete this mapping?"
                        onConfirm={() => router.delete(route('admin.coa-mappings.destroy', record.id))}
                    >
                        <Button size="small" danger>
                            Delete
                        </Button>
                    </Popconfirm>
                </Space>
            ),
        },
    ];

    return (
        <AuthenticatedLayout title="CoA Mappings">
            <Head title="CoA Mappings" />
            <Link href={route('admin.coa-mappings.create')}>
                <Button type="primary" style={{ marginBottom: 16 }}>
                    Create
                </Button>
            </Link>
            <ProTable
                rowKey="id"
                search={false}
                options={false}
                pagination={false}
                columns={columns}
                dataSource={mappings}
            />
        </AuthenticatedLayout>
    );
}
