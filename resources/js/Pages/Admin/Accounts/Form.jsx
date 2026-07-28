import { Head, useForm } from '@inertiajs/react';
import { Button, Checkbox, Form, Input, InputNumber, Select } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

const accountTypes = [
    'revenue',
    'backcharge',
    'cost_of_sales',
    'employee_expense',
    'admin_expense',
    'depreciation',
    'other',
].map((type) => ({ value: type, label: type.replace(/_/g, ' ') }));

export default function FormPage({ account, parents }) {
    const isEdit = Boolean(account);
    const title = isEdit ? 'Edit Account' : 'Create Account';

    const { data, setData, post, put, processing, errors } = useForm({
        sap_code: account?.sap_code ?? '',
        name: account?.name ?? '',
        parent_id: account?.parent_id ?? null,
        account_type: account?.account_type ?? 'cost_of_sales',
        normal_balance: account?.normal_balance ?? 'debit',
        level: account?.level ?? 0,
        is_postable: account?.is_postable ?? true,
        sort_order: account?.sort_order ?? 0,
    });

    const submit = () => {
        if (isEdit) {
            put(route('admin.accounts.update', account.id));
        } else {
            post(route('admin.accounts.store'));
        }
    };

    return (
        <AuthenticatedLayout title={title}>
            <Head title={title} />
            <Form layout="vertical" onFinish={submit} style={{ maxWidth: 520 }}>
                <Form.Item label="SAP Code" validateStatus={errors.sap_code ? 'error' : ''} help={errors.sap_code}>
                    <Input value={data.sap_code} onChange={(e) => setData('sap_code', e.target.value)} />
                </Form.Item>
                <Form.Item label="Name" validateStatus={errors.name ? 'error' : ''} help={errors.name}>
                    <Input value={data.name} onChange={(e) => setData('name', e.target.value)} />
                </Form.Item>
                <Form.Item label="Parent" help={errors.parent_id}>
                    <Select
                        allowClear
                        value={data.parent_id}
                        onChange={(value) => setData('parent_id', value)}
                        options={parents.map((parent) => ({
                            value: parent.id,
                            label: `${parent.sap_code} — ${parent.name}`,
                        }))}
                    />
                </Form.Item>
                <Form.Item label="Account Type" help={errors.account_type}>
                    <Select
                        value={data.account_type}
                        onChange={(value) => setData('account_type', value)}
                        options={accountTypes}
                    />
                </Form.Item>
                <Form.Item label="Normal Balance" help={errors.normal_balance}>
                    <Select
                        value={data.normal_balance}
                        onChange={(value) => setData('normal_balance', value)}
                        options={[
                            { value: 'debit', label: 'Debit' },
                            { value: 'credit', label: 'Credit' },
                        ]}
                    />
                </Form.Item>
                <Form.Item label="Level" help={errors.level}>
                    <InputNumber
                        value={data.level}
                        onChange={(value) => setData('level', value)}
                        style={{ width: '100%' }}
                        min={0}
                    />
                </Form.Item>
                <Form.Item>
                    <Checkbox
                        checked={data.is_postable}
                        onChange={(e) => setData('is_postable', e.target.checked)}
                    >
                        Postable
                    </Checkbox>
                </Form.Item>
                <Form.Item label="Sort Order" help={errors.sort_order}>
                    <InputNumber
                        value={data.sort_order}
                        onChange={(value) => setData('sort_order', value)}
                        style={{ width: '100%' }}
                        min={0}
                    />
                </Form.Item>
                <Button type="primary" htmlType="submit" loading={processing}>
                    {isEdit ? 'Update' : 'Create'}
                </Button>
            </Form>
        </AuthenticatedLayout>
    );
}
