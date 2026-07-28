import { Head, router } from '@inertiajs/react';
import { ProTable } from '@ant-design/pro-components';
import { Button, Descriptions, Tag } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { formatCurrency } from '@/Utils/currency';

export default function Show({ journal }) {
    const columns = [
        { title: 'Account', render: (_, r) => r.account?.sap_code },
        { title: 'Debit', dataIndex: 'debit', render: (v) => formatCurrency(v) },
        { title: 'Credit', dataIndex: 'credit', render: (v) => formatCurrency(v) },
        { title: 'Memo', dataIndex: 'memo' },
    ];

    return (
        <AuthenticatedLayout title={`Journal ${journal.reference_no}`}>
            <Head title={`Journal ${journal.reference_no}`} />
            <Descriptions bordered size="small" style={{ marginBottom: 16 }}>
                <Descriptions.Item label="Site">{journal.project_site?.code}</Descriptions.Item>
                <Descriptions.Item label="Status"><Tag>{journal.status}</Tag></Descriptions.Item>
            </Descriptions>
            {journal.status === 'pending_approval' && (
                <div style={{ marginBottom: 16 }}>
                    <Button type="primary" onClick={() => router.post(`/journals/${journal.id}/approve`)}>Approve</Button>
                </div>
            )}
            <ProTable rowKey="id" search={false} options={false} columns={columns} dataSource={journal.lines} pagination={false} />
        </AuthenticatedLayout>
    );
}
