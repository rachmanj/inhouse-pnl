import { usePage } from '@inertiajs/react';

export function usePermission() {
    const { auth } = usePage().props;

    const can = (permission) => {
        if (!auth?.user?.permissions) {
            return false;
        }
        return auth.user.permissions.includes(permission);
    };

    const hasRole = (role) => {
        if (!auth?.user?.roles) {
            return false;
        }
        return auth.user.roles.includes(role);
    };

    return { can, hasRole };
}
