import { Head, router } from '@inertiajs/react';
import { ProTable } from '@ant-design/pro-components';
import { Button, Select, Tag } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Index({ periods }) {
    const columns = [
        { title: 'Period', render: (_, r) => `${r.year}-${String(r.month).padStart(2,'0')}` },
        { title: 'Status', dataIndex: 'status', render: (s) => <Tag>{s}</Tag> },
        { title: 'Actions', render: (_, r) => (
            <Select size="small" placeholder="Transition" style={{ width: 140 }} onChange={(status) => router.patch(`/admin/report-periods/${r.id}/status`, { status })}>
                <Select.Option value="in_review">In Review</Select.Option>
                <Select.Option value="approved">Approved</Select.Option>
                <Select.Option value="locked">Locked</Select.Option>
            </Select>
        )},
    ];

    return (
        <AuthenticatedLayout title="Report Periods">
            <Head title="Report Periods" />
            <ProTable rowKey="id" search={false} options={false} columns={columns} dataSource={periods} pagination={false} />
        </AuthenticatedLayout>
    );
}
