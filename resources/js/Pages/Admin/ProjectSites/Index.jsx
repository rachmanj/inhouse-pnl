import { Head, Link, router } from '@inertiajs/react';
import { ProTable } from '@ant-design/pro-components';
import { Button, Popconfirm, Space, Tag } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Index({ sites }) {
    const columns = [
        { title: 'Code', dataIndex: 'code', key: 'code' },
        { title: 'Name', dataIndex: 'name', key: 'name' },
        { title: 'Type', dataIndex: 'type', key: 'type' },
        { title: 'Region', dataIndex: 'region', key: 'region' },
        {
            title: 'Active',
            dataIndex: 'is_active',
            key: 'is_active',
            render: (value) => (
                <Tag color={value ? 'green' : 'default'}>{value ? 'Yes' : 'No'}</Tag>
            ),
        },
        { title: 'Sort', dataIndex: 'sort_order', key: 'sort_order' },
        {
            title: 'Actions',
            key: 'actions',
            render: (_, record) => (
                <Space>
                    <Link href={route('admin.project-sites.edit', record.id)}>
                        <Button size="small">Edit</Button>
                    </Link>
                    <Popconfirm
                        title="Delete this site?"
                        onConfirm={() => router.delete(route('admin.project-sites.destroy', record.id))}
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
        <AuthenticatedLayout title="Project Sites">
            <Head title="Project Sites" />
            <Link href={route('admin.project-sites.create')}>
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
                dataSource={sites}
            />
        </AuthenticatedLayout>
    );
}
