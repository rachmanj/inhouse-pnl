import { Head } from '@inertiajs/react';
import { ProTable } from '@ant-design/pro-components';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { formatCurrency } from '@/Utils/currency';

export default function Payments({ filing, payments }) {
    const columns = [
        { title: 'Date', dataIndex: 'payment_date' },
        { title: 'Amount', dataIndex: 'amount', render: (v) => formatCurrency(v) },
        { title: 'Reference', dataIndex: 'payment_reference' },
    ];

    return (
        <AuthenticatedLayout title={`Payments — ${filing.tax_type}`}>
            <Head title="Tax Payments" />
            <ProTable rowKey="id" search={false} options={false} columns={columns} dataSource={payments.data} pagination={{ total: payments.total, pageSize: 50 }} />
        </AuthenticatedLayout>
    );
}
