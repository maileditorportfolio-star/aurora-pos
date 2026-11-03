import { Button } from '@/components/ui/button';
import { Head, useForm } from '@inertiajs/react';

interface Product {
    id: number;
    name: string;
    selling_price: number;
}

interface Props {
    products: Product[];
}

export default function PosIndex({ products }: Props) {
    const { data, setData, post, processing } = useForm({
        items: [] as Array<{ product_id: number; quantity: number }>,
        payments: [{ amount: 0, method: 'cash' }],
    });

    const addItem = (product: Product) => {
        const existing = data.items.find((i) => i.product_id === product.id);
        if (existing) {
            const items = data.items.map((i) =>
                i.product_id === product.id ? { ...i, quantity: i.quantity + 1 } : i,
            );
            setData('items', items);
        } else {
            setData('items', [...data.items, { product_id: product.id, quantity: 1 }]);
        }
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        const total = data.items.reduce((sum, item) => {
            const p = products.find((pr) => pr.id === item.product_id);
            return sum + (p ? Number(p.selling_price) * item.quantity : 0);
        }, 0);
        setData('payments', [{ amount: total, method: 'cash' }]);
        setTimeout(() => post(route('pos.checkout')), 0);
    };

    return (
        <>
            <Head title="POS" />
            <div className="grid grid-cols-3 gap-4 p-4">
                <div className="col-span-2 grid grid-cols-4 gap-2">
                    {products.map((product) => (
                        <Button key={product.id} variant="outline" onClick={() => addItem(product)}>
                            {product.name}
                        </Button>
                    ))}
                </div>
                <div>
                    <form onSubmit={submit}>
                        <Button type="submit" disabled={processing || data.items.length === 0}>
                            Checkout
                        </Button>
                    </form>
                </div>
            </div>
        </>
    );
}
