import { Head, Link } from '@inertiajs/react';
import CustomToast from '@/components/ui/custom-toast';

export default function Welcome() {

    return (
        <>
            <Head title="Welcome">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
            </Head>
            <div className={'flex flex-col items-center justify-center h-screen'}>
                <h1>Hello World</h1>
                <Link href={route('toast-test.index')}>Rota de teste de toast</Link>
                <CustomToast />
            </div>
        </>
    );
}
