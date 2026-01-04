export const isValidCPF = (input: string): boolean => {
    if (!input) return false;

    // remove tudo que não for número
    const cpf = input.replace(/\D/g, '');

    // precisa ter 11 dígitos
    if (cpf.length !== 11) return false;

    // bloqueia CPFs com todos os dígitos iguais (000..., 111..., etc)
    if (/^(\d)\1{10}$/.test(cpf)) return false;

    const calcCheckDigit = (base: string, factorStart: number) => {
        let sum = 0;
        for (let i = 0; i < base.length; i++) {
            sum += Number(base[i]) * (factorStart - i);
        }
        const mod = sum % 11;
        return mod < 2 ? 0 : 11 - mod;
    };

    const base9 = cpf.slice(0, 9);
    const digit1 = calcCheckDigit(base9, 10);
    if (digit1 !== Number(cpf[9])) return false;

    const base10 = cpf.slice(0, 10);
    const digit2 = calcCheckDigit(base10, 11);
    if (digit2 !== Number(cpf[10])) return false;

    return true;
};
