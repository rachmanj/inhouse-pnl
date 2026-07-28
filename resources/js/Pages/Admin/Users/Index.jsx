import { Head, Link, router } from '@inertiajs/react';
import { ProTable } from '@ant-design/pro-components';
import { Button, Popconfirm, Space, Tag } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Index({ users }) {
    const columns = [
        { title: 'Name', dataIndex: 'name', key: 'name' },
        { title: 'Email', dataIndex: 'email', key: 'email' },
        {
            title: 'Roles',
            key: 'roles',
            render: (_, record) =>
                record.roles?.map((role) => <Tag key={role.id}>{role.name}</Tag>),
        },
        {
            title: 'Sites',
            key: 'sites',
            render: (_, record) => record.project_sites?.map((site) => site.code).join(', ') || '-',
        },
        {
            title: 'Actions',
            key: 'actions',
            render: (_, record) => (
                <Space>
                    <Link href={route('admin.users.edit', record.id)}>
                        <Button size="small">Edit</Button>
                    </Link>
                    <Popconfirm
                        title="Delete this user?"
                        onConfirm={() => router.delete(route('admin.users.destroy', record.id))}
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
        <AuthenticatedLayout title="Users">
            <Head title="Users" />
            <Link href={route('admin.users.create')}>
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
                dataSource={users}
            />
        </AuthenticatedLayout>
    );
}
