import { Head } from '@inertiajs/react';
import { Table, Tag } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { formatCurrency } from '@/Utils/currency';

export default function Calendar({ filings }) {
    const columns = [
        { title: 'Type', dataIndex: 'tax_type' },
        { title: 'Due Date', dataIndex: 'due_date' },
        { title: 'Amount', dataIndex: 'amount_reported', render: (v) => formatCurrency(v) },
        { title: 'Status', dataIndex: 'status', render: (s) => <Tag color={s === 'late' ? 'red' : 'default'}>{s}</Tag> },
    ];

    return (
        <AuthenticatedLayout title="Tax Calendar">
            <Head title="Tax Calendar" />
            <Table rowKey="id" columns={columns} dataSource={filings} pagination={{ pageSize: 20 }} />
        </AuthenticatedLayout>
    );
}
