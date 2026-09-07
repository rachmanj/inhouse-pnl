import GuestLayout from '@/Layouts/GuestLayout';
import { Head, useForm } from '@inertiajs/react';
import { Alert, Button, Form, Input, Typography } from 'antd';

export default function ForgotPassword({ status }) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    const submit = () => {
        post(route('password.email'));
    };

    return (
        <GuestLayout>
            <Head title="Forgot Password" />

            <Typography.Paragraph type="secondary" style={{ marginBottom: 16 }}>
                Forgot your password? No problem. Just let us know your email address and we
                will email you a password reset link that will allow you to choose a new one.
            </Typography.Paragraph>

            {status && (
                <Alert type="success" message={status} showIcon style={{ marginBottom: 16 }} />
            )}

            <Form layout="vertical" onFinish={submit}>
                <Form.Item
                    label="Email"
                    validateStatus={errors.email ? 'error' : ''}
                    help={errors.email}
                >
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        autoFocus
                        onChange={(e) => setData('email', e.target.value)}
                    />
                </Form.Item>

                <Form.Item>
                    <Button type="primary" htmlType="submit" block loading={processing}>
                        Email Password Reset Link
                    </Button>
                </Form.Item>
            </Form>
        </GuestLayout>
    );
}
