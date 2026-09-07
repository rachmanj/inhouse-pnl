import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { Alert, Button, Typography } from 'antd';

export default function VerifyEmail({ status }) {
    const { post, processing } = useForm({});

    const submit = () => {
        post(route('verification.send'));
    };

    return (
        <GuestLayout>
            <Head title="Email Verification" />

            <Typography.Paragraph type="secondary" style={{ marginBottom: 16 }}>
                Thanks for signing up! Before getting started, could you verify your email
                address by clicking on the link we just emailed to you? If you didn&apos;t
                receive the email, we will gladly send you another.
            </Typography.Paragraph>

            {status === 'verification-link-sent' && (
                <Alert
                    type="success"
                    message="A new verification link has been sent to the email address you provided during registration."
                    showIcon
                    style={{ marginBottom: 16 }}
                />
            )}

            <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                <Button type="primary" loading={processing} onClick={submit} block>
                    Resend Verification Email
                </Button>

                <div style={{ textAlign: 'center' }}>
                    <Link href={route('logout')} method="post" as="button">
                        <Typography.Link>Log Out</Typography.Link>
                    </Link>
                </div>
            </div>
        </GuestLayout>
    );
}
