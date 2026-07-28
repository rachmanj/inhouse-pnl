import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Create() {
    return (
        <AuthenticatedLayout title="Create Journal">
            <Head title="Create Journal" />
            <p>Journal entry form — coming soon</p>
        </AuthenticatedLayout>
    );
}
