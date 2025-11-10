import { Button } from '@/components/ui/button';
import { router } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

interface AppHeaderProps {
    title: string;
    description: string;
    routeBack: string;
}
export default function AppHeader({ title, description, routeBack }: AppHeaderProps) {
    return (
        <div className="flex items-center justify-between">
            <div>
                <h1 className="text-3xl font-bold">{title}</h1>
                <p className="text-muted-foreground">{description}</p>
            </div>
            <Button variant="outline" onClick={() => router.get(routeBack)}>
                <ArrowLeft className="mr-2 h-4 w-4" />
                Voltar
            </Button>
        </div>
    );
}
