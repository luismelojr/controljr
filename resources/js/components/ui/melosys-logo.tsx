interface MeloSysLogoProps {
    className?: string;
    showText?: boolean;
}

export function MeloSysLogo({ className = 'h-8 w-8', showText = false }: MeloSysLogoProps) {
    return (
        <div className="flex items-center gap-2">
            <svg className={className} viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                {/* Background circle */}
                <circle cx="50" cy="50" r="48" fill="#000000" />

                {/* M letter with upward arrow design */}
                <path
                    d="M25 65V38L35 48L45 35L55 48L65 35L75 48V65M35 48V65M55 48V65"
                    stroke="#ffffff"
                    strokeWidth="4"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                />

                {/* Accent dots representing data points */}
                <circle cx="35" cy="50" r="2" fill="#ffffff" opacity="0.8" />
                <circle cx="45" cy="43" r="2" fill="#ffffff" opacity="0.8" />
                <circle cx="55" cy="50" r="2" fill="#ffffff" opacity="0.8" />
                <circle cx="65" cy="43" r="2" fill="#ffffff" opacity="0.8" />
            </svg>

            {showText && <span className="text-lg font-semibold">MeloSys</span>}
        </div>
    );
}
