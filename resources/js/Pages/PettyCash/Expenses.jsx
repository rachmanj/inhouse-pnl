import { Head, useForm } from '@inertiajs/react';
import { ProTable } from '@ant-design/pro-components';
import { Button, DatePicker, Drawer, Form, Input, InputNumber, Tag } from 'antd';
import dayjs from 'dayjs';
import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { formatCurrency } from '@/Utils/currency';

export default function Expenses({ fund, expenses = [] }) {
    const [drawerOpen, setDrawerOpen] = useState(false);
    const { data, setData, post, processing, reset, errors } = useForm({
        expense_date: dayjs().format('YYYY-MM-DD'),
        category: '',
        description: '',
        amount: 0,
    });

    const columns = [
        {
            title: 'Date',
            dataIndex: 'expense_date',
            valueType: 'date',
        },
        { title: 'Category', dataIndex: 'category' },
        { title: 'Description', dataIndex: 'description', ellipsis: true },
        {
            title: 'Amount',
            dataIndex: 'amount',
            align: 'right',
            render: (v) => formatCurrency(v),
        },
        {
            title: 'Source',
            dataIndex: 'source',
            render: (source, record) => (
                source === 'email_import' ? (
                    <Tag color="blue">
                        Email
                        {record.import_batch_id && ` #${record.import_batch_id}`}
                    </Tag>
                ) : (
                    <Tag>Manual</Tag>
                )
            ),
        },
    ];

    const submit = () => {
        post(route('petty-cash.expenses.store', fund.id), {
            onSuccess: () => {
                reset();
                setDrawerOpen(false);
            },
        });
    };

    return (
        <AuthenticatedLayout title={`Petty Cash — ${fund.project_site?.code ?? fund.site?.code}`}>
            <Head title="Petty Cash Expenses" />

            <Button type="primary" style={{ marginBottom: 16 }} onClick={() => setDrawerOpen(true)}>
                Add Expense
            </Button>

            <ProTable
                rowKey="id"
                search={false}
                options={false}
                columns={columns}
                dataSource={expenses}
                pagination={{ pageSize: 20 }}
            />

            <Drawer
                title="Add Expense"
                open={drawerOpen}
                onClose={() => setDrawerOpen(false)}
                width={400}
            >
                <Form layout="vertical" onFinish={submit}>
                    <Form.Item label="Date" validateStatus={errors.expense_date ? 'error' : ''} help={errors.expense_date}>
                        <DatePicker
                            style={{ width: '100%' }}
                            value={data.expense_date ? dayjs(data.expense_date) : null}
                            onChange={(d) => setData('expense_date', d?.format('YYYY-MM-DD'))}
                        />
                    </Form.Item>
                    <Form.Item label="Category" validateStatus={errors.category ? 'error' : ''} help={errors.category}>
                        <Input value={data.category} onChange={(e) => setData('category', e.target.value)} />
                    </Form.Item>
                    <Form.Item label="Description" help={errors.description}>
                        <Input.TextArea value={data.description} onChange={(e) => setData('description', e.target.value)} />
                    </Form.Item>
                    <Form.Item label="Amount" validateStatus={errors.amount ? 'error' : ''} help={errors.amount}>
                        <InputNumber
                            style={{ width: '100%' }}
                            min={0}
                            value={data.amount}
                            onChange={(v) => setData('amount', v)}
                        />
                    </Form.Item>
                    <Button type="primary" htmlType="submit" loading={processing} block>
                        Save
                    </Button>
                </Form>
            </Drawer>
        </AuthenticatedLayout>
    );
}
