import { Head, Link, router } from '@inertiajs/react';
import { ProTable } from '@ant-design/pro-components';
import { Button, Space } from 'antd';
import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import FilingStatusBadge from '@/Components/Tax/FilingStatusBadge';
import TaxTypeTabs from '@/Components/Tax/TaxTypeTabs';
import PeriodSelector from '@/Components/Shared/PeriodSelector';
import { formatCurrency } from '@/Utils/currency';
import dayjs from 'dayjs';

export default function Index({ filings = [], taxType: initialTaxType = 'ppn' }) {
    const [taxType, setTaxType] = useState(initialTaxType);

    const filtered = filings.filter((f) => f.tax_type === taxType);

    const columns = [
        {
            title: 'Site',
            render: (_, record) => record.project_site?.code ?? record.site?.code ?? 'Entity',
        },
        { title: 'Filing No', dataIndex: 'filing_number' },
        {
            title: 'Due Date',
            dataIndex: 'due_date',
            render: (d) => dayjs(d).format('DD MMM YYYY'),
        },
        {
            title: 'Amount',
            dataIndex: 'amount_reported',
            align: 'right',
            render: (v) => formatCurrency(v),
        },
        {
            title: 'Status',
            dataIndex: 'status',
            render: (status, record) => (
                <FilingStatusBadge status={status} dueDate={record.due_date} />
            ),
        },
        { title: 'Source', dataIndex: 'source' },
        {
            title: 'Actions',
            key: 'actions',
            render: (_, record) => (
                <Link href={route('tax.payments.index', record.id)}>
                    <Button size="small">Payments</Button>
                </Link>
            ),
        },
    ];

    const handleTabChange = (key) => {
        setTaxType(key);
        router.get(route('tax.index'), { tax_type: key }, { preserveState: true, preserveScroll: true });
    };

    return (
        <AuthenticatedLayout title="Tax Filings">
            <Head title="Tax" />
            <Space style={{ marginBottom: 16 }} wrap>
                <PeriodSelector />
                <Link href={route('tax.calendar')}>
                    <Button>Due Date Calendar</Button>
                </Link>
            </Space>

            <TaxTypeTabs activeKey={taxType} onChange={handleTabChange}>
                {() => (
                    <ProTable
                        rowKey="id"
                        search={false}
                        options={false}
                        columns={columns}
                        dataSource={filtered}
                        pagination={{ pageSize: 20 }}
                    />
                )}
            </TaxTypeTabs>
        </AuthenticatedLayout>
    );
}
