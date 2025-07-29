interface ActivityLogData {
    activity_type: string;
    module: string;
    description: string;
    activity_data?: any;
}

class ActivityLogger {
    /**
     * Log user activity to the backend
     */
    static async logActivity(data: ActivityLogData): Promise<void> {
        try {
            const response = await fetch('/api/log-activity', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify(data),
            });

            if (!response.ok) {
                console.error('Failed to log activity:', response.statusText);
            }
        } catch (error) {
            console.error('Error logging activity:', error);
        }
    }

    /**
     * Log Manajemen Anggaran activities
     */
    static async logManajemenAnggaran(action: string, details?: any): Promise<void> {
        await this.logActivity({
            activity_type: 'anggaran', // Sesuai dengan UserActivityLog::TYPE_ANGGARAN
            module: 'manajemen_anggaran', // Sesuai dengan UserActivityLog::MODULE_MANAJEMEN_ANGGARAN
            description: action,
            activity_data: details,
        });
    }

    /**
     * Log Rencana Awal activities
     */
    static async logRencanaAwal(action: string, details?: any): Promise<void> {
        await this.logActivity({
            activity_type: 'rencana_awal', // Sesuai dengan UserActivityLog::TYPE_RENCANA_AWAL
            module: 'rencana_awal', // Sesuai dengan UserActivityLog::MODULE_RENCANA_AWAL
            description: action,
            activity_data: details,
        });
    }

    /**
     * Log Triwulan activities
     */
    static async logTriwulan(action: string, triwulanName: string, details?: any): Promise<void> {
        await this.logActivity({
            activity_type: 'triwulan', // Sesuai dengan UserActivityLog::TYPE_TRIWULAN
            module: 'triwulan', // Sesuai dengan UserActivityLog::MODULE_TRIWULAN  
            description: `${action} pada ${triwulanName}`,
            activity_data: { ...details, triwulan_name: triwulanName },
        });
    }

    /**
     * Log PDF download activities
     */
    static async logPDFDownload(documentType: string, details?: any): Promise<void> {
        await this.logActivity({
            activity_type: 'pdf_download', // Sesuai dengan UserActivityLog::TYPE_PDF_DOWNLOAD
            module: 'monitoring', // Sesuai dengan UserActivityLog::MODULE_MONITORING
            description: `Mengunduh PDF ${documentType}`,
            activity_data: { ...details, pdf_type: documentType },
        });
    }
}

export default ActivityLogger; 