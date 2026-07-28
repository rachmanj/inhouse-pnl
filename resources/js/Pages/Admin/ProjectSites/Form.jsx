import { Head, useForm } from '@inertiajs/react';
import { Button, Checkbox, Form, Input, InputNumber, Select } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

const siteTypes = [
    { value: 'mining', label: 'Mining' },
    { value: 'quarry', label: 'Quarry' },
    { value: 'services', label: 'Services' },
    { value: 'admin', label: 'Admin' },
];

export default function FormPage({ site }) {
    const isEdit = Boolean(site);
    const title = isEdit ? 'Edit Project Site' : 'Create Project Site';

    const { data, setData, post, put, processing, errors } = useForm({
        code: site?.code ?? '',
        name: site?.name ?? '',
        type: site?.type ?? 'mining',
        region: site?.region ?? '',
        is_active: site?.is_active ?? true,
        sort_order: site?.sort_order ?? 0,
    });

    const submit = () => {
        if (isEdit) {
            put(route('admin.project-sites.update', site.id));
        } else {
            post(route('admin.project-sites.store'));
        }
    };

    return (
        <AuthenticatedLayout title={title}>
            <Head title={title} />
            <Form layout="vertical" onFinish={submit} style={{ maxWidth: 480 }}>
                <Form.Item
                    label="Code"
                    validateStatus={errors.code ? 'error' : ''}
                    help={errors.code}
                >
                    <Input value={data.code} onChange={(e) => setData('code', e.target.value)} />
                </Form.Item>
                <Form.Item
                    label="Name"
                    validateStatus={errors.name ? 'error' : ''}
                    help={errors.name}
                >
                    <Input value={data.name} onChange={(e) => setData('name', e.target.value)} />
                </Form.Item>
                <Form.Item label="Type" validateStatus={errors.type ? 'error' : ''} help={errors.type}>
                    <Select
                        value={data.type}
                        onChange={(value) => setData('type', value)}
                        options={siteTypes}
                    />
                </Form.Item>
                <Form.Item label="Region" help={errors.region}>
                    <Input value={data.region} onChange={(e) => setData('region', e.target.value)} />
                </Form.Item>
                <Form.Item>
                    <Checkbox
                        checked={data.is_active}
                        onChange={(e) => setData('is_active', e.target.checked)}
                    >
                        Active
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
