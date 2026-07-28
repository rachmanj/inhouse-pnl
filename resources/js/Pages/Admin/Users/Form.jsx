import { Head, useForm } from '@inertiajs/react';
import { Button, Checkbox, Form, Input, Select } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function FormPage({ user, roles, sites }) {
    const isEdit = Boolean(user);
    const title = isEdit ? 'Edit User' : 'Create User';

    const { data, setData, post, put, processing, errors } = useForm({
        name: user?.name ?? '',
        email: user?.email ?? '',
        password: '',
        dark_mode: user?.dark_mode ?? true,
        is_active: user?.is_active ?? true,
        role: user?.roles?.[0]?.name ?? roles[0]?.name ?? '',
        project_site_ids: user?.project_sites?.map((site) => site.id) ?? [],
    });

    const submit = () => {
        if (isEdit) {
            put(route('admin.users.update', user.id));
        } else {
            post(route('admin.users.store'));
        }
    };

    return (
        <AuthenticatedLayout title={title}>
            <Head title={title} />
            <Form layout="vertical" onFinish={submit} style={{ maxWidth: 520 }}>
                <Form.Item label="Name" validateStatus={errors.name ? 'error' : ''} help={errors.name}>
                    <Input value={data.name} onChange={(e) => setData('name', e.target.value)} />
                </Form.Item>
                <Form.Item label="Email" validateStatus={errors.email ? 'error' : ''} help={errors.email}>
                    <Input value={data.email} onChange={(e) => setData('email', e.target.value)} />
                </Form.Item>
                <Form.Item
                    label={isEdit ? 'Password (leave blank to keep)' : 'Password'}
                    validateStatus={errors.password ? 'error' : ''}
                    help={errors.password}
                >
                    <Input.Password
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                    />
                </Form.Item>
                <Form.Item label="Role" validateStatus={errors.role ? 'error' : ''} help={errors.role}>
                    <Select
                        value={data.role}
                        onChange={(value) => setData('role', value)}
                        options={roles.map((role) => ({ value: role.name, label: role.name }))}
                    />
                </Form.Item>
                <Form.Item label="Project Sites" help={errors.project_site_ids}>
                    <Select
                        mode="multiple"
                        value={data.project_site_ids}
                        onChange={(value) => setData('project_site_ids', value)}
                        options={sites.map((site) => ({
                            value: site.id,
                            label: `${site.code} — ${site.name}`,
                        }))}
                    />
                </Form.Item>
                <Form.Item>
                    <Checkbox
                        checked={data.dark_mode}
                        onChange={(e) => setData('dark_mode', e.target.checked)}
                    >
                        Dark Mode
                    </Checkbox>
                </Form.Item>
                <Form.Item>
                    <Checkbox
                        checked={data.is_active}
                        onChange={(e) => setData('is_active', e.target.checked)}
                    >
                        Active
                    </Checkbox>
                </Form.Item>
                <Button type="primary" htmlType="submit" loading={processing}>
                    {isEdit ? 'Update' : 'Create'}
                </Button>
            </Form>
        </AuthenticatedLayout>
    );
}
