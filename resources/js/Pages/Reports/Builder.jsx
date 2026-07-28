import { Head, useForm } from '@inertiajs/react';
import { Button, Form, Select } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Builder({ periods }) {
    const { data, setData, post, processing } = useForm({ report_period_id: null });

    return (
        <AuthenticatedLayout title="Report Builder">
            <Head title="Report Builder" />
            <Form layout="vertical" onFinish={() => post('/reports')} style={{ maxWidth: 480 }}>
                <Form.Item label="Period">
                    <Select value={data.report_period_id} onChange={(v) => setData('report_period_id', v)}
                        options={periods.map((p) => ({ value: p.id, label: `${p.year}-${String(p.month).padStart(2,'0')}` }))} />
                </Form.Item>
                <Button type="primary" htmlType="submit" loading={processing}>Create Package</Button>
            </Form>
        </AuthenticatedLayout>
    );
}
