import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { Alert, Button, Checkbox, Form, Input, Typography } from 'antd';

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = () => {
        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    const hasAuthError = errors.email || errors.password;

    return (
        <GuestLayout>
            <Head title="Log in" />

            {status && (
                <Alert type="success" message={status} showIcon style={{ marginBottom: 16 }} />
            )}

            {hasAuthError && (
                <Alert
                    type="error"
                    message={errors.email || errors.password || 'Authentication failed.'}
                    showIcon
                    style={{ marginBottom: 16 }}
                />
            )}

            <Form layout="vertical" onFinish={submit}>
                <Form.Item
                    label="Email"
                    validateStatus={errors.email ? 'error' : ''}
                    help={errors.email && !hasAuthError ? errors.email : undefined}
                >
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        autoComplete="username"
                        autoFocus
                        onChange={(e) => setData('email', e.target.value)}
                    />
                </Form.Item>

                <Form.Item
                    label="Password"
                    validateStatus={errors.password ? 'error' : ''}
                    help={errors.password && !hasAuthError ? errors.password : undefined}
                >
                    <Input.Password
                        id="password"
                        name="password"
                        value={data.password}
                        autoComplete="current-password"
                        onChange={(e) => setData('password', e.target.value)}
                    />
                </Form.Item>

                <Form.Item>
                    <Checkbox
                        checked={data.remember}
                        onChange={(e) => setData('remember', e.target.checked)}
                    >
                        Remember me
                    </Checkbox>
                </Form.Item>

                <Form.Item>
                    <Button type="primary" htmlType="submit" block loading={processing}>
                        Log in
                    </Button>
                </Form.Item>

                {canResetPassword && (
                    <div style={{ textAlign: 'center' }}>
                        <Link href={route('password.request')}>
                            <Typography.Link>Forgot your password?</Typography.Link>
                        </Link>
                    </div>
                )}
            </Form>
        </GuestLayout>
    );
}
