import { Head, Link } from '@inertiajs/react';
import { ProTable } from '@ant-design/pro-components';
import { Button } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Index({ roles }) {
    const columns = [
        { title: 'Role', dataIndex: 'name', key: 'name' },
        {
            title: 'Permissions',
            key: 'permissions',
            render: (_, record) => record.permissions?.length ?? 0,
        },
        {
            title: 'Actions',
            key: 'actions',
            render: (_, record) => (
                <Link href={route('admin.roles.edit', record.id)}>
                    <Button size="small">Edit</Button>
                </Link>
            ),
        },
    ];

    return (
        <AuthenticatedLayout title="Roles">
            <Head title="Roles" />
            <ProTable
                rowKey="id"
                search={false}
                options={false}
                pagination={false}
                columns={columns}
                dataSource={roles}
            />
        </AuthenticatedLayout>
    );
}
