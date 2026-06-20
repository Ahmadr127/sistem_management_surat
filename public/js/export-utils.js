/**
 * export-utils.js
 * ---------------
 * Library terpusat untuk export file di sisi client (browser).
 * Mendukung: Excel (.xlsx), PDF, dan ZIP.
 *
 * Dependensi (harus di-include via CDN sebelum file ini):
 *  - SheetJS  : https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js
 *  - jsPDF    : https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js
 *  - AutoTable: https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js
 *  - JSZip    : https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js
 *  - FileSaver: https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js
 */

const ExportUtils = (() => {

    // =========================================================================
    // 1. EXPORT TO EXCEL (.xlsx) menggunakan SheetJS
    // =========================================================================

    /**
     * Export data ke file Excel (.xlsx).
     *
     * @param {Array<Object>} data        - Array objek data (satu objek = satu baris)
     * @param {Array<Object>} columns     - Definisi kolom: [{ header: 'Nama Kolom', key: 'field_name' }]
     * @param {string}        filename    - Nama file tanpa ekstensi (contoh: 'Laporan Surat Keluar')
     * @param {string}        sheetTitle  - Judul sheet di dalam Excel
     */
    function exportToExcel(data, columns, filename = 'Export', sheetTitle = 'Sheet1') {
        if (typeof XLSX === 'undefined') {
            console.error('SheetJS (XLSX) library belum dimuat!');
            alert('Gagal export: Library SheetJS belum tersedia.');
            return;
        }

        // Susun header baris pertama
        const headers = columns.map(col => col.header);

        // Susun baris data
        const rows = data.map(item =>
            columns.map(col => {
                const val = col.key.split('.').reduce((obj, k) => (obj && obj[k] !== undefined ? obj[k] : null), item);
                return val !== null && val !== undefined ? val : '-';
            })
        );

        // Gabungkan header + rows menjadi array of arrays
        const wsData = [headers, ...rows];

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(wsData);

        // Auto-width kolom
        const colWidths = headers.map((h, i) => {
            const maxLen = Math.max(
                h.length,
                ...rows.map(row => String(row[i] ?? '').length)
            );
            return { wch: Math.min(maxLen + 4, 60) };
        });
        ws['!cols'] = colWidths;

        // Style header (bold) - SheetJS CE tidak support styling penuh,
        // tapi kita bisa set nama sheet
        XLSX.utils.book_append_sheet(wb, ws, sheetTitle.substring(0, 31));

        const safeFilename = filename.replace(/[/\\?%*:|"<>]/g, '-');
        XLSX.writeFile(wb, `${safeFilename}.xlsx`);
    }


    // =========================================================================
    // 2. EXPORT TO PDF menggunakan jsPDF + AutoTable
    // =========================================================================

    /**
     * Export data ke file PDF menggunakan jsPDF dan AutoTable.
     *
     * @param {Array<Object>} data       - Array objek data
     * @param {Array<Object>} columns    - Definisi kolom: [{ header: 'Nama Kolom', key: 'field_name' }]
     * @param {string}        title      - Judul laporan (ditampilkan di header PDF)
     * @param {Object}        metadata   - Info tambahan: { startDate, endDate, periodType, createdBy }
     * @param {string}        filename   - Nama file tanpa ekstensi
     */
    function exportToPDF(data, columns, title = 'Laporan', metadata = {}, filename = 'Laporan') {
        if (typeof window.jspdf === 'undefined' && typeof jsPDF === 'undefined') {
            console.error('jsPDF library belum dimuat!');
            alert('Gagal export: Library jsPDF belum tersedia.');
            return;
        }

        const { jsPDF: JsPDF } = window.jspdf || { jsPDF: window.jsPDF };
        const doc = new JsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });

        const pageWidth = doc.internal.pageSize.getWidth();
        let currentY = 15;

        // --- Header ---
        doc.setFontSize(16);
        doc.setFont('helvetica', 'bold');
        doc.setTextColor(22, 163, 74); // hijau
        doc.text(title, pageWidth / 2, currentY, { align: 'center' });
        currentY += 7;

        doc.setFontSize(9);
        doc.setFont('helvetica', 'normal');
        doc.setTextColor(80, 80, 80);
        doc.text('Sistem Informasi Surat Menyurat Azra', pageWidth / 2, currentY, { align: 'center' });
        currentY += 8;

        // --- Meta Info ---
        doc.setFontSize(9);
        doc.setTextColor(50, 50, 50);

        const periodLabel = metadata.periodType === 'monthly' ? 'Bulanan'
                          : metadata.periodType === 'weekly'  ? 'Mingguan'
                          : 'Kustom';

        const metaLines = [
            `Periode  : ${periodLabel}`,
            `Tanggal  : ${metadata.startDate || '-'} s/d ${metadata.endDate || '-'}`,
            `Dibuat   : ${metadata.createdBy || '-'}  |  Tanggal Cetak: ${new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })}`,
        ];

        metaLines.forEach(line => {
            doc.text(line, 14, currentY);
            currentY += 5;
        });
        currentY += 3;

        // --- Tabel ---
        const head = [columns.map(col => col.header)];
        const body = data.map(item =>
            columns.map(col => {
                const val = col.key.split('.').reduce((obj, k) => (obj && obj[k] !== undefined ? obj[k] : null), item);
                return val !== null && val !== undefined ? String(val) : '-';
            })
        );

        doc.autoTable({
            head: head,
            body: body,
            startY: currentY,
            theme: 'grid',
            styles: {
                fontSize: 7.5,
                cellPadding: 2.5,
                valign: 'middle',
                overflow: 'linebreak',
            },
            headStyles: {
                fillColor: [22, 163, 74],
                textColor: 255,
                fontStyle: 'bold',
                fontSize: 8,
            },
            alternateRowStyles: {
                fillColor: [249, 249, 249],
            },
            margin: { left: 14, right: 14 },
            columnStyles: columns.reduce((acc, col, i) => {
                if (col.width) acc[i] = { cellWidth: col.width };
                return acc;
            }, {}),
            didDrawPage: function (hookData) {
                // Footer dengan nomor halaman
                const pageCount = doc.internal.getNumberOfPages();
                doc.setFontSize(8);
                doc.setTextColor(150);
                doc.text(
                    `Halaman ${hookData.pageNumber} dari ${pageCount}`,
                    pageWidth / 2,
                    doc.internal.pageSize.getHeight() - 8,
                    { align: 'center' }
                );
            }
        });

        const safeFilename = filename.replace(/[/\\?%*:|"<>]/g, '-');
        doc.save(`${safeFilename}.pdf`);
    }


    // =========================================================================
    // 3. DOWNLOAD MULTIPLE FILES AS ZIP menggunakan JSZip + FileSaver
    // =========================================================================

    /**
     * Download beberapa file dari URL lalu packing menjadi satu file ZIP.
     *
     * @param {Array<Object>} files       - Array file: [{ url: '/path/to/file', name: 'filename.pdf' }]
     * @param {string}        zipFilename - Nama file ZIP tanpa ekstensi
     * @param {Function}      onProgress  - Callback progress opsional: (percent) => void
     */
    async function downloadFilesAsZip(files, zipFilename = 'lampiran', onProgress = null) {
        if (typeof JSZip === 'undefined') {
            console.error('JSZip library belum dimuat!');
            alert('Gagal membuat ZIP: Library JSZip belum tersedia.');
            return;
        }
        if (typeof saveAs === 'undefined') {
            console.error('FileSaver.js library belum dimuat!');
            alert('Gagal download: Library FileSaver.js belum tersedia.');
            return;
        }

        if (!files || files.length === 0) {
            alert('Tidak ada file yang dapat di-download.');
            return;
        }

        const zip = new JSZip();
        const total = files.length;
        let completed = 0;

        for (const fileInfo of files) {
            try {
                const response = await fetch(fileInfo.url);
                if (!response.ok) {
                    console.warn(`Gagal mengunduh: ${fileInfo.url} (status: ${response.status})`);
                    continue;
                }
                const blob = await response.blob();
                zip.file(fileInfo.name, blob);
                completed++;
                if (onProgress) onProgress(Math.round((completed / total) * 100));
            } catch (err) {
                console.error(`Error saat mengunduh file: ${fileInfo.url}`, err);
            }
        }

        if (Object.keys(zip.files).length === 0) {
            alert('Tidak ada file yang berhasil diunduh. Periksa apakah file masih tersedia di server.');
            return;
        }

        try {
            const content = await zip.generateAsync({ type: 'blob' });
            const safeFilename = zipFilename.replace(/[/\\?%*:|"<>]/g, '-');
            saveAs(content, `${safeFilename}.zip`);
        } catch (err) {
            console.error('Gagal membuat file ZIP:', err);
            alert('Terjadi kesalahan saat membuat file ZIP.');
        }
    }


    // =========================================================================
    // Expose public API
    // =========================================================================
    return {
        exportToExcel,
        exportToPDF,
        downloadFilesAsZip,
    };
})();
