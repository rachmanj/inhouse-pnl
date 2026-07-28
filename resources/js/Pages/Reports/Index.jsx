import { Head, Link } from '@inertiajs/react';
import { ProTable } from '@ant-design/pro-components';
import { Button, Tag } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Index({ packages }) {
    const columns = [
        { title: 'ID', dataIndex: 'id' },
        { title: 'Period', render: (_, r) => r.report_period ? `${r.report_period.year}-${String(r.report_period.month).padStart(2,'0')}` : '-' },
        { title: 'Status', dataIndex: 'status', render: (s) => <Tag>{s}</Tag> },
        { title: 'Actions', render: (_, r) => <Link href={`/reports/${r.id}`}><Button size="small">Open</Button></Link> },
    ];

    return (
        <AuthenticatedLayout title="Reports">
            <Head title="Reports" />
            <Link href="/reports/create"><Button type="primary" style={{ marginBottom: 16 }}>New Package</Button></Link>
            <ProTable rowKey="id" search={false} options={false} columns={columns} dataSource={packages.data} pagination={{ total: packages.total }} />
        </AuthenticatedLayout>
    );
}
