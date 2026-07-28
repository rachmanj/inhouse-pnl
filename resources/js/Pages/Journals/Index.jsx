import { Head, Link } from '@inertiajs/react';
import { ProTable } from '@ant-design/pro-components';
import { Button, Tag } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Index({ journals }) {
    const columns = [
        { title: 'Reference', dataIndex: 'reference_no' },
        { title: 'Site', render: (_, r) => r.project_site?.code },
        { title: 'Status', dataIndex: 'status', render: (s) => <Tag>{s}</Tag> },
        { title: 'Actions', render: (_, r) => <Link href={`/journals/${r.id}`}><Button size="small">View</Button></Link> },
    ];

    return (
        <AuthenticatedLayout title="Journals">
            <Head title="Journals" />
            <Link href="/journals/create"><Button type="primary" style={{ marginBottom: 16 }}>Create Journal</Button></Link>
            <ProTable rowKey="id" search={false} options={false} columns={columns} dataSource={journals.data} pagination={{ total: journals.total }} />
        </AuthenticatedLayout>
    );
}
