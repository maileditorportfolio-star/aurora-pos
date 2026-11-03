import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { type HeadlessInertiaFormData } from '@inertiajs/react';
import { Head, useForm } from '@inertiajs/react';

interface Store {
    id: number;
    name: string;
    code: string;
}

interface Props {
    stores: Store[];
}

export default function SelectStore({ stores }: Props) {
    const { data, setData, post, processing } = useForm({
        store_id: stores[0]?.id || '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('stores.select.store'));
    };

    return (
        <>
            <Head title="Select Store" />
            <div className="flex min-h-screen items-center justify-center bg-background p-4">
                <Card className="w-full max-w-md">
                    <CardHeader>
                        <CardTitle>Select Store</CardTitle>
                        <CardDescription>Choose a store to continue</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="space-y-4">
                            <select
                                className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                value={data.store_id}
                                onChange={(e) => setData('store_id', e.target.value as unknown as HeadlessInertiaFormData)}
                            >
                                {stores.map((store) => (
                                    <option key={store.id} value={store.id}>
                                        {store.name} ({store.code})
                                    </option>
                                ))}
                            </select>
                            <Button type="submit" className="w-full" disabled={processing}>
                                Continue
                            </Button>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
