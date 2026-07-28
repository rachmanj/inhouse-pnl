import { Head, Link, useForm } from '@inertiajs/react';
import { ProDescriptions, ProTable } from '@ant-design/pro-components';
import { Button, DatePicker, Drawer, Form, Input, InputNumber } from 'antd';
import dayjs from 'dayjs';
import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import FilingStatusBadge from '@/Components/Tax/FilingStatusBadge';
import { formatCurrency } from '@/Utils/currency';

export default function Payments({ filing, payments = [], pagination = {} }) {
    const [drawerOpen, setDrawerOpen] = useState(false);
    const { data, setData, post, processing, reset, errors } = useForm({
        payment_date: dayjs().format('YYYY-MM-DD'),
        amount: 0,
        payment_reference: '',
    });

    const columns = [
        {
            title: 'Payment Date',
            dataIndex: 'payment_date',
            valueType: 'date',
        },
        {
            title: 'Amount',
            dataIndex: 'amount',
            align: 'right',
            render: (v) => formatCurrency(v),
        },
        { title: 'Reference', dataIndex: 'payment_reference' },
    ];

    const submit = () => {
        post(route('tax.payments.store', filing.id), {
            onSuccess: () => {
                reset();
                setDrawerOpen(false);
            },
        });
    };

    return (
        <AuthenticatedLayout title={`Tax Payments — ${filing.tax_type?.toUpperCase()}`}>
            <Head title="Tax Payments" />

            <Link href={route('tax.index')} style={{ marginBottom: 16, display: 'inline-block' }}>
                ← Back to filings
            </Link>

            <ProDescriptions
                column={3}
                style={{ marginBottom: 16 }}
                dataSource={filing}
                columns={[
                    { title: 'Tax Type', dataIndex: 'tax_type', render: (v) => v?.toUpperCase() },
                    {
                        title: 'Site',
                        render: () => filing.project_site?.code ?? filing.site?.code ?? 'Entity',
                    },
                    { title: 'Filing No', dataIndex: 'filing_number' },
                    {
                        title: 'Due Date',
                        dataIndex: 'due_date',
                        render: (d) => dayjs(d).format('DD MMM YYYY'),
                    },
                    {
                        title: 'Amount Reported',
                        dataIndex: 'amount_reported',
                        render: (v) => formatCurrency(v),
                    },
                    {
                        title: 'Status',
                        dataIndex: 'status',
                        render: (s) => <FilingStatusBadge status={s} dueDate={filing.due_date} />,
                    },
                ]}
            />

            <Button type="primary" style={{ marginBottom: 16 }} onClick={() => setDrawerOpen(true)}>
                Record Payment
            </Button>

            <ProTable
                rowKey="id"
                search={false}
                options={false}
                columns={columns}
                dataSource={payments}
                pagination={{
                    current: pagination.current_page,
                    pageSize: pagination.per_page,
                    total: pagination.total,
                }}
            />

            <Drawer title="Record Payment" open={drawerOpen} onClose={() => setDrawerOpen(false)} width={400}>
                <Form layout="vertical" onFinish={submit}>
                    <Form.Item label="Payment Date" validateStatus={errors.payment_date ? 'error' : ''} help={errors.payment_date}>
                        <DatePicker
                            style={{ width: '100%' }}
                            value={data.payment_date ? dayjs(data.payment_date) : null}
                            onChange={(d) => setData('payment_date', d?.format('YYYY-MM-DD'))}
                        />
                    </Form.Item>
                    <Form.Item label="Amount" validateStatus={errors.amount ? 'error' : ''} help={errors.amount}>
                        <InputNumber style={{ width: '100%' }} min={0} value={data.amount} onChange={(v) => setData('amount', v)} />
                    </Form.Item>
                    <Form.Item label="Reference" help={errors.payment_reference}>
                        <Input value={data.payment_reference} onChange={(e) => setData('payment_reference', e.target.value)} />
                    </Form.Item>
                    <Button type="primary" htmlType="submit" loading={processing} block>
                        Save Payment
                    </Button>
                </Form>
            </Drawer>
        </AuthenticatedLayout>
    );
}
