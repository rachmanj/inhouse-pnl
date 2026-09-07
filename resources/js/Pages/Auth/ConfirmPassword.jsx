import GuestLayout from '@/Layouts/GuestLayout';
import { Head, useForm } from '@inertiajs/react';
import { Button, Form, Input, Typography } from 'antd';

export default function ConfirmPassword() {
    const { data, setData, post, processing, errors, reset } = useForm({
        password: '',
    });

    const submit = () => {
        post(route('password.confirm'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <GuestLayout>
            <Head title="Confirm Password" />

            <Typography.Paragraph type="secondary" style={{ marginBottom: 16 }}>
                This is a secure area of the application. Please confirm your password before
                continuing.
            </Typography.Paragraph>

            <Form layout="vertical" onFinish={submit}>
                <Form.Item
                    label="Password"
                    validateStatus={errors.password ? 'error' : ''}
                    help={errors.password}
                >
                    <Input.Password
                        id="password"
                        name="password"
                        value={data.password}
                        autoFocus
                        onChange={(e) => setData('password', e.target.value)}
                    />
                </Form.Item>

                <Form.Item>
                    <Button type="primary" htmlType="submit" block loading={processing}>
                        Confirm
                    </Button>
                </Form.Item>
            </Form>
        </GuestLayout>
    );
}
