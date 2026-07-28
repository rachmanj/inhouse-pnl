import { Head, useForm } from '@inertiajs/react';
import { ProForm, ProFormSelect, ProFormText, ProFormTextArea } from '@ant-design/pro-components';
import { Button, Space } from 'antd';
import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import JournalBalanceBadge from '@/Components/Journals/JournalBalanceBadge';
import JournalLineTable from '@/Components/Journals/JournalLineTable';

const emptyLine = () => ({ account_id: null, debit: 0, credit: 0, memo: '' });

export default function Create({ periods = [], sites = [], accounts = [] }) {
    const [lines, setLines] = useState([emptyLine(), emptyLine()]);

    const { data, setData, post, processing, errors } = useForm({
        report_period_id: null,
        project_site_id: null,
        reference_no: '',
        description: '',
        lines: [],
    });

    const totalDebit = lines.reduce((s, l) => s + Number(l.debit || 0), 0);
    const totalCredit = lines.reduce((s, l) => s + Number(l.credit || 0), 0);

    const submit = () => {
        setData('lines', lines);
        post(route('journals.store'));
    };

    return (
        <AuthenticatedLayout title="New Journal Entry">
            <Head title="New Journal" />
            <ProForm
                submitter={false}
                onFinish={submit}
                style={{ maxWidth: 900 }}
            >
                <ProFormSelect
                    name="report_period_id"
                    label="Report Period"
                    rules={[{ required: true }]}
                    options={periods.map((p) => ({
                        value: p.id,
                        label: `${p.year}-${String(p.month).padStart(2, '0')}`,
                    }))}
                    fieldProps={{
                        value: data.report_period_id,
                        onChange: (v) => setData('report_period_id', v),
                    }}
                />
                <ProFormSelect
                    name="project_site_id"
                    label="Project Site"
                    rules={[{ required: true }]}
                    options={sites.map((s) => ({
                        value: s.id,
                        label: `${s.code} — ${s.name}`,
                    }))}
                    fieldProps={{
                        value: data.project_site_id,
                        onChange: (v) => setData('project_site_id', v),
                    }}
                />
                <ProFormText
                    name="reference_no"
                    label="Reference No"
                    rules={[{ required: true }]}
                    fieldProps={{
                        value: data.reference_no,
                        onChange: (e) => setData('reference_no', e.target.value),
                    }}
                />
                <ProFormTextArea
                    name="description"
                    label="Description"
                    fieldProps={{
                        value: data.description,
                        onChange: (e) => setData('description', e.target.value),
                    }}
                />
            </ProForm>

            <div style={{ margin: '16px 0' }}>
                <JournalBalanceBadge totalDebit={totalDebit} totalCredit={totalCredit} />
            </div>

            <JournalLineTable lines={lines} editable onChange={setLines} />

            <Space style={{ marginTop: 16 }}>
                <Button type="dashed" onClick={() => setLines([...lines, emptyLine()])}>
                    Add Line
                </Button>
                <Button type="primary" loading={processing} onClick={submit}>
                    Save Journal
                </Button>
            </Space>
            {errors.lines && <div style={{ color: '#ff4d4f', marginTop: 8 }}>{errors.lines}</div>}
        </AuthenticatedLayout>
    );
}
