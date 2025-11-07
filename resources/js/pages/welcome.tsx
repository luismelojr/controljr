import { Button } from '@/components/ui/button';
import CustomToast from '@/components/ui/custom-toast';
import TextAreaCustom from '@/components/ui/text-area-custom';
import TextInput from '@/components/ui/text-input';
import TextMask from '@/components/ui/text-mask';
import TextMultiSelect from '@/components/ui/text-multi-select';
import TextSelect from '@/components/ui/text-select';
import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';

export default function Welcome() {
    const [selectedTechs, setSelectedTechs] = useState<string[]>([]);
    const [selectedLanguage, setSelectedLanguage] = useState<string>('');
    const [name, setName] = useState<string>('');
    const [phone, setPhone] = useState<string>('');
    const [bio, setBio] = useState<string>('');

    const techOptions = [
        { value: 'react', label: 'React' },
        { value: 'vue', label: 'Vue.js' },
        { value: 'angular', label: 'Angular' },
        { value: 'typescript', label: 'TypeScript' },
        { value: 'javascript', label: 'JavaScript' },
        { value: 'tailwind', label: 'Tailwind CSS' },
        { value: 'laravel', label: 'Laravel' },
        { value: 'nodejs', label: 'Node.js' },
        { value: 'docker', label: 'Docker' },
        { value: 'postgres', label: 'PostgreSQL' },
    ];

    const languageOptions = [
        { value: 'pt-br', label: 'Português (Brasil)' },
        { value: 'en', label: 'English' },
        { value: 'es', label: 'Español' },
    ];

    return (
        <>
            <Head title="Welcome">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
            </Head>
            <div className={'flex min-h-screen flex-col items-center justify-center p-8'}>
                <div className="w-full max-w-2xl space-y-8">
                    <div className="text-center">
                        <h1 className="mb-2 text-3xl font-bold">Exemplos de Componentes</h1>
                        <p className="text-muted-foreground">Demonstração dos componentes de formulário personalizados</p>
                    </div>

                    <div className="space-y-6 rounded-lg border p-6">
                        <TextInput
                            id="name"
                            label="Nome"
                            type="text"
                            placeholder="Digite seu nome"
                            value={name}
                            onChange={(e) => setName(e.target.value)}
                        />

                        <TextSelect
                            id="language"
                            label="Idioma"
                            placeholder="Selecione um idioma"
                            options={languageOptions}
                            value={selectedLanguage}
                            onValueChange={setSelectedLanguage}
                            required
                        />

                        <TextMask
                            label="Telefone"
                            id="phone"
                            mask="(00) 00000-0000"
                            placeholder="(00) 00000-0000"
                            value={phone}
                            onChange={setPhone}
                        />

                        <TextMultiSelect
                            id="technologies"
                            label="Tecnologias"
                            placeholder="Selecione suas tecnologias favoritas"
                            searchPlaceholder="Buscar tecnologia..."
                            emptyText="Nenhuma tecnologia encontrada."
                            options={techOptions}
                            selected={selectedTechs}
                            onChange={setSelectedTechs}
                            required
                        />

                        <TextAreaCustom
                            id="bio"
                            label="Biografia"
                            placeholder="Conte-nos um pouco sobre você..."
                            value={bio}
                            onChange={(e) => setBio(e.target.value)}
                            rows={4}
                        />

                        <div className="pt-4">
                            <Button
                                onClick={() => {
                                    console.log({
                                        name,
                                        phone,
                                        language: selectedLanguage,
                                        technologies: selectedTechs,
                                        bio,
                                    });
                                }}
                                className="w-full"
                            >
                                Ver dados no console
                            </Button>
                        </div>
                    </div>

                    <div className="space-y-2 text-center">
                        <Link href={route('toast-test.index')} className="text-sm text-muted-foreground underline hover:text-foreground">
                            Rota de teste de toast
                        </Link>
                    </div>
                </div>
                <CustomToast />
            </div>
        </>
    );
}
