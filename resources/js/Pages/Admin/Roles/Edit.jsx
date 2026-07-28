import { Head, useForm } from '@inertiajs/react';
import { Button, Checkbox, Form, Space } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Edit({ role, permissions }) {
    const { data, setData, put, processing, errors } = useForm({
        permissions: role.permissions?.map((permission) => permission.name) ?? [],
    });

    const togglePermission = (name, checked) => {
        setData(
            'permissions',
            checked
                ? [...data.permissions, name]
                : data.permissions.filter((permission) => permission !== name),
        );
    };

    const submit = () => {
        put(route('admin.roles.update', role.id));
    };

    return (
        <AuthenticatedLayout title={`Edit Role: ${role.name}`}>
            <Head title={`Edit Role: ${role.name}`} />
            <Form layout="vertical" onFinish={submit} style={{ maxWidth: 640 }}>
                {errors.permissions && (
                    <div style={{ color: '#ff4d4f', marginBottom: 16 }}>{errors.permissions}</div>
                )}
                <Space direction="vertical" style={{ width: '100%' }}>
                    {permissions.map((permission) => (
                        <Checkbox
                            key={permission.id}
                            checked={data.permissions.includes(permission.name)}
                            onChange={(e) => togglePermission(permission.name, e.target.checked)}
                            disabled={role.name === 'Super Admin'}
                        >
                            {permission.name}
                        </Checkbox>
                    ))}
                </Space>
                <Button
                    type="primary"
                    htmlType="submit"
                    loading={processing}
                    disabled={role.name === 'Super Admin'}
                    style={{ marginTop: 16 }}
                >
                    Save Permissions
                </Button>
            </Form>
        </AuthenticatedLayout>
    );
}
