import { Head, router } from '@inertiajs/react';
import { Steps } from 'antd';
import { useState } from 'react';
import ConfirmStep from '@/Components/Import/ConfirmStep';
import MapColumnsStep from '@/Components/Import/MapColumnsStep';
import PreviewStep from '@/Components/Import/PreviewStep';
import UploadStep from '@/Components/Import/UploadStep';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

const STEPS = [
    { title: 'Upload', description: 'Select file & period' },
    { title: 'Map', description: 'Confirm columns' },
    { title: 'Preview', description: 'Review staging' },
    { title: 'Confirm', description: 'Upsert balances' },
];

export default function Create({
    periods = [],
    sites = [],
    batch = null,
    columnMap = {},
    detectedColumns = [],
    stagingRows = [],
    summary = {},
    mappingCounts = {},
}) {
    const [current, setCurrent] = useState(batch?.status === 'validated' ? 3 : batch ? 1 : 0);
    const [loading, setLoading] = useState(false);

    const handleUpload = async (formData) => {
        setLoading(true);
        router.post(route('imports.store'), formData, {
            forceFormData: true,
            onFinish: () => setLoading(false),
        });
    };

    const handleMap = async (values) => {
        if (!batch?.id) {
            return;
        }
        setLoading(true);
        router.post(route('imports.resolve-mapping', batch.id), { column_map: values }, {
            onFinish: () => setLoading(false),
            onSuccess: () => setCurrent(2),
        });
    };

    const handlePreviewContinue = () => setCurrent(3);

    const handleConfirm = () => {
        if (!batch?.id) {
            return;
        }
        setLoading(true);
        router.post(route('imports.confirm', batch.id), {}, {
            onFinish: () => setLoading(false),
        });
    };

    return (
        <AuthenticatedLayout title="New Import">
            <Head title="New Import" />
            <Steps current={current} items={STEPS} style={{ marginBottom: 32 }} />

            {current === 0 && (
                <UploadStep
                    periods={periods}
                    sites={sites}
                    onSubmit={handleUpload}
                    loading={loading}
                />
            )}
            {current === 1 && (
                <MapColumnsStep
                    columnMap={columnMap}
                    detectedColumns={detectedColumns}
                    onSubmit={handleMap}
                    loading={loading}
                />
            )}
            {current === 2 && (
                <PreviewStep
                    summary={summary}
                    stagingRows={stagingRows}
                    mappingCounts={mappingCounts}
                    onSubmit={handlePreviewContinue}
                    loading={loading}
                />
            )}
            {current === 3 && (
                <ConfirmStep
                    batch={batch}
                    summary={summary}
                    onConfirm={handleConfirm}
                    loading={loading}
                />
            )}
        </AuthenticatedLayout>
    );
}
