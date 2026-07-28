import { Head, Link, router } from '@inertiajs/react';
import { ProTable } from '@ant-design/pro-components';
import { Button, Popconfirm, Space } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Index({ accounts }) {
    const columns = [
        { title: 'SAP Code', dataIndex: 'sap_code', key: 'sap_code' },
        { title: 'Name', dataIndex: 'name', key: 'name' },
        { title: 'Type', dataIndex: 'account_type', key: 'account_type' },
        { title: 'Balance', dataIndex: 'normal_balance', key: 'normal_balance' },
        {
            title: 'Parent',
            key: 'parent',
            render: (_, record) => record.parent?.sap_code ?? '-',
        },
        {
            title: 'Actions',
            key: 'actions',
            render: (_, record) => (
                <Space>
                    <Link href={route('admin.accounts.edit', record.id)}>
                        <Button size="small">Edit</Button>
                    </Link>
                    <Popconfirm
                        title="Delete this account?"
                        onConfirm={() => router.delete(route('admin.accounts.destroy', record.id))}
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
        <AuthenticatedLayout title="Accounts">
            <Head title="Accounts" />
            <Link href={route('admin.accounts.create')}>
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
                dataSource={accounts}
            />
        </AuthenticatedLayout>
    );
}
