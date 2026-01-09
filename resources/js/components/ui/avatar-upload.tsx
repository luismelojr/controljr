import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { Button } from "@/components/ui/button"
import { usePage } from "@inertiajs/react"
import { Camera, Trash2, Upload } from "lucide-react"
import { useRef, useState } from "react"

interface AvatarUploadProps {
    currentAvatarUrl?: string | null
    onFileSelect: (file: File | null) => void
    error?: string
}

export function AvatarUpload({ currentAvatarUrl, onFileSelect, error }: AvatarUploadProps) {
    const { auth } = usePage().props
    const [previewUrl, setPreviewUrl] = useState<string | null>(currentAvatarUrl ? `/storage/${currentAvatarUrl}` : null)
    const fileInputRef = useRef<HTMLInputElement>(null)

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0]
        if (file) {
            const objectUrl = URL.createObjectURL(file)
            setPreviewUrl(objectUrl)
            onFileSelect(file)
        }
    }

    const handleRemove = () => {
        setPreviewUrl(null)
        onFileSelect(null)
        if (fileInputRef.current) {
            fileInputRef.current.value = ""
        }
    }

    const triggerFileInput = () => {
        fileInputRef.current?.click()
    }

    const initials = auth.user.name
        .split(" ")
        .map((n: string) => n[0])
        .join("")
        .toUpperCase()
        .substring(0, 2)

    return (
        <div className="flex flex-col items-center gap-4">
            <div className="relative group">
                <Avatar className="h-32 w-32 cursor-pointer ring-4 ring-background shadow-xl" onClick={triggerFileInput}>
                    <AvatarImage src={previewUrl || undefined} className="object-cover" />
                    <AvatarFallback className="text-2xl bg-primary/10 text-primary font-bold">
                        {initials}
                    </AvatarFallback>
                </Avatar>
                
                <div 
                    className="absolute inset-0 flex items-center justify-center bg-black/40 rounded-full opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer text-white"
                    onClick={triggerFileInput}
                >
                    <Camera className="w-8 h-8" />
                </div>

                <div className="absolute bottom-0 right-0">
                    <Button 
                        size="icon" 
                        variant="secondary" 
                        className="h-8 w-8 rounded-full shadow-md"
                        onClick={triggerFileInput}
                    >
                        <Upload className="h-4 w-4" />
                    </Button>
                </div>
            </div>

            <input
                type="file"
                ref={fileInputRef}
                className="hidden"
                accept="image/*"
                onChange={handleFileChange}
            />

            {previewUrl && (
                <Button 
                    variant="ghost" 
                    size="sm" 
                    className="text-destructive hover:text-destructive hover:bg-destructive/10"
                    onClick={handleRemove}
                >
                    <Trash2 className="w-4 h-4 mr-2" />
                    Remover foto
                </Button>
            )}

            {error && <p className="text-sm text-destructive font-medium">{error}</p>}
        </div>
    )
}
