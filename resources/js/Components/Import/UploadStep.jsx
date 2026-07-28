import { InboxOutlined } from '@ant-design/icons';
import { ProForm, ProFormSelect, ProFormUploadDragger } from '@ant-design/pro-components';
import { Alert } from 'antd';

export default function UploadStep({ periods = [], sites = [], onSubmit, loading = false }) {
    return (
        <ProForm
            submitter={{
                searchConfig: { submitText: 'Upload & Stage' },
                submitButtonProps: { loading },
            }}
            onFinish={async (values) => {
                const formData = new FormData();
                formData.append('report_period_id', values.report_period_id);
                if (values.project_site_id) {
                    formData.append('project_site_id', values.project_site_id);
                }
                const file = values.file?.[0]?.originFileObj;
                if (file) {
                    formData.append('file', file);
                }
                await onSubmit?.(formData, values);
            }}
        >
            <Alert
                type="info"
                showIcon
                style={{ marginBottom: 16 }}
                message="Upload a SAP export (.xlsx). The parser detects the SAP marker row and column headers automatically."
            />
            <ProFormSelect
                name="report_period_id"
                label="Report Period"
                rules={[{ required: true, message: 'Select a period' }]}
                options={periods.map((p) => ({
                    value: p.id,
                    label: `${p.year}-${String(p.month).padStart(2, '0')}`,
                }))}
            />
            <ProFormSelect
                name="project_site_id"
                label="Project Site"
                rules={[{ required: true, message: 'Select a site' }]}
                options={sites.map((s) => ({
                    value: s.id,
                    label: `${s.code} — ${s.name}`,
                }))}
            />
            <ProFormUploadDragger
                name="file"
                label="SAP Export File"
                max={1}
                accept=".xlsx,.xls"
                rules={[{ required: true, message: 'Upload a file' }]}
                fieldProps={{
                    beforeUpload: () => false,
                    listType: 'text',
                }}
                icon={<InboxOutlined />}
                description="Drag & drop or click to select an Excel file"
            />
        </ProForm>
    );
}
