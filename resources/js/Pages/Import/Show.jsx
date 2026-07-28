import { Head, router } from '@inertiajs/react';
import { ProTable } from '@ant-design/pro-components';
import { Button, Descriptions, Tag } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Show({ batch }) {
    const columns = [
        { title: '#', dataIndex: 'row_number', width: 60 },
        { title: 'Account', dataIndex: 'raw_account_code' },
        { title: 'Name', dataIndex: 'raw_account_name' },
        { title: 'Debit', dataIndex: 'raw_debit' },
        { title: 'Credit', dataIndex: 'raw_credit' },
        { title: 'Balance', dataIndex: 'raw_balance' },
        { title: 'Status', dataIndex: 'mapping_status', render: (s) => <Tag>{s}</Tag> },
        { title: 'Mapped', render: (_, r) => r.mapped_account?.sap_code ?? '-' },
    ];

    return (
        <AuthenticatedLayout title={`Import #${batch.id}`}>
            <Head title={`Import #${batch.id}`} />
            <Descriptions bordered size="small" style={{ marginBottom: 16 }}>
                <Descriptions.Item label="Status">{batch.status}</Descriptions.Item>
                <Descriptions.Item label="File">{batch.original_filename}</Descriptions.Item>
                <Descriptions.Item label="Rows">{batch.mapped_rows}/{batch.total_rows}</Descriptions.Item>
            </Descriptions>
            {batch.status === 'validated' && (
                <Button type="primary" style={{ marginBottom: 16 }} onClick={() => router.post(`/imports/${batch.id}/confirm`)}>
                    Confirm Import
                </Button>
            )}
            <ProTable rowKey="id" search={false} options={false} columns={columns} dataSource={batch.staging_rows} pagination={{ pageSize: 50 }} />
        </AuthenticatedLayout>
    );
}
