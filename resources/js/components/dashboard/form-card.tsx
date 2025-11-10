export default function FormCard({ children }: React.PropsWithChildren) {
    return <div className={'rounded-lg border bg-card p-6'}>{children}</div>;
}
