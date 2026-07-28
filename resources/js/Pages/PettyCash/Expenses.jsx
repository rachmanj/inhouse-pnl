import { Head } from '@inertiajs/react';
import { ProTable } from '@ant-design/pro-components';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { formatCurrency } from '@/Utils/currency';

export default function Expenses({ fund, expenses }) {
    const columns = [
        { title: 'Date', dataIndex: 'expense_date' },
        { title: 'Category', dataIndex: 'category' },
        { title: 'Description', dataIndex: 'description' },
        { title: 'Amount', dataIndex: 'amount', render: (v) => formatCurrency(v) },
        { title: 'Source', dataIndex: 'source' },
    ];

    return (
        <AuthenticatedLayout title={`Petty Cash — ${fund.project_site?.code}`}>
            <Head title="Petty Cash Expenses" />
            <ProTable rowKey="id" search={false} options={false} columns={columns} dataSource={expenses.data} pagination={{ total: expenses.total }} />
        </AuthenticatedLayout>
    );
}
