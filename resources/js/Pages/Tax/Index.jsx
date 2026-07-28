import { Head, Link } from '@inertiajs/react';
import { ProTable } from '@ant-design/pro-components';
import { Tabs, Tag } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { formatCurrency } from '@/Utils/currency';

export default function Index({ filings }) {
    const columns = [
        { title: 'Type', dataIndex: 'tax_type' },
        { title: 'Site', render: (_, r) => r.project_site?.code ?? 'Entity' },
        { title: 'Due Date', dataIndex: 'due_date' },
        { title: 'Amount', dataIndex: 'amount_reported', render: (v) => formatCurrency(v) },
        { title: 'Status', dataIndex: 'status', render: (s) => <Tag>{s}</Tag> },
        { title: 'Actions', render: (_, r) => <Link href={`/tax/${r.id}/payments`}>Payments</Link> },
    ];

    const types = ['ppn', 'pph21', 'pph23', 'pph25', 'pph4a2'];

    return (
        <AuthenticatedLayout title="Tax">
            <Head title="Tax" />
            <Tabs items={types.map((t) => ({
                key: t,
                label: t.toUpperCase(),
                children: <ProTable rowKey="id" search={false} options={false} columns={columns}
                    dataSource={filings.data.filter((f) => f.tax_type === t)} pagination={false} />,
            }))} />
        </AuthenticatedLayout>
    );
}
