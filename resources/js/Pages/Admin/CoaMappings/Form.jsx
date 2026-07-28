import { Head, useForm } from '@inertiajs/react';
import { Button, DatePicker, Form, InputNumber, Select } from 'antd';
import dayjs from 'dayjs';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function FormPage({ mapping, accounts, pnlLines }) {
    const isEdit = Boolean(mapping);
    const title = isEdit ? 'Edit CoA Mapping' : 'Create CoA Mapping';

    const { data, setData, post, put, processing, errors } = useForm({
        account_id: mapping?.account_id ?? null,
        pnl_line_id: mapping?.pnl_line_id ?? null,
        effective_from: mapping?.effective_from ?? '2024-01-01',
        version: mapping?.version ?? 1,
    });

    const submit = () => {
        if (isEdit) {
            put(route('admin.coa-mappings.update', mapping.id));
        } else {
            post(route('admin.coa-mappings.store'));
        }
    };

    return (
        <AuthenticatedLayout title={title}>
            <Head title={title} />
            <Form layout="vertical" onFinish={submit} style={{ maxWidth: 520 }}>
                <Form.Item label="Account" validateStatus={errors.account_id ? 'error' : ''} help={errors.account_id}>
                    <Select
                        showSearch
                        optionFilterProp="label"
                        value={data.account_id}
                        onChange={(value) => setData('account_id', value)}
                        options={accounts.map((account) => ({
                            value: account.id,
                            label: `${account.sap_code} — ${account.name}`,
                        }))}
                    />
                </Form.Item>
                <Form.Item label="P&L Line" validateStatus={errors.pnl_line_id ? 'error' : ''} help={errors.pnl_line_id}>
                    <Select
                        showSearch
                        optionFilterProp="label"
                        value={data.pnl_line_id}
                        onChange={(value) => setData('pnl_line_id', value)}
                        options={pnlLines.map((line) => ({
                            value: line.id,
                            label: `${line.code} — ${line.name}`,
                        }))}
                    />
                </Form.Item>
                <Form.Item
                    label="Effective From"
                    validateStatus={errors.effective_from ? 'error' : ''}
                    help={errors.effective_from}
                >
                    <DatePicker
                        style={{ width: '100%' }}
                        value={data.effective_from ? dayjs(data.effective_from) : null}
                        onChange={(date) => setData('effective_from', date?.format('YYYY-MM-DD'))}
                    />
                </Form.Item>
                <Form.Item label="Version" help={errors.version}>
                    <InputNumber
                        value={data.version}
                        onChange={(value) => setData('version', value)}
                        style={{ width: '100%' }}
                        min={1}
                    />
                </Form.Item>
                <Button type="primary" htmlType="submit" loading={processing}>
                    {isEdit ? 'Update' : 'Create'}
                </Button>
            </Form>
        </AuthenticatedLayout>
    );
}
