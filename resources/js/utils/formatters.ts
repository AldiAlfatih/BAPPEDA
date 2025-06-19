/**
 * Format angka ke dalam format rupiah
 */
export const formatRupiah = (value: number): string => {
  if (!value && value !== 0) return 'Rp 0';
  
  const numberString = value.toString().replace(/[^,\d]/g, '');
  const split = numberString.split(',');
  let sisa = split[0].length % 3;
  let rupiah = split[0].substr(0, sisa);
  const ribuan = split[0].substr(sisa).match(/\d{3}/g);

  if (ribuan) {
    rupiah += (sisa ? '.' : '') + ribuan.join('.');
  }

  return 'Rp ' + rupiah + (split[1] ? ',' + split[1] : '');
};

/**
 * Format angka ke dalam format persentase
 */
export const formatPercent = (value: number | string): string => {
  if (value === '' || value === undefined || value === null) return '';
  const numValue = parseFloat(String(value).replace(',', '.'));
  if (isNaN(numValue)) return '0%';
  return numValue.toFixed(2) + '%';
};

/**
 * Handler untuk input rupiah - menghapus karakter non-numerik
 */
export const onInputRupiah = (event: Event): number => {
  const input = event.target as HTMLInputElement;
  const rawValue = input.value.replace(/[^\d]/g, ''); // Hanya ambil angka
  return parseInt(rawValue) || 0;
};

/**
 * Handler untuk input persentase - menghapus karakter non-numerik dan membatasi max 100
 */
export const onInputPercent = (event: Event): number => {
  const input = event.target as HTMLInputElement;
  let rawValue = input.value.replace(/[^\d.,]/g, ''); // Hanya ambil angka dan tanda desimal
  rawValue = rawValue.replace(',', '.'); // Ubah koma menjadi titik untuk decimal

  // Pastikan nilai tidak melebihi 100%
  const numericValue = parseFloat(rawValue);
  if (isNaN(numericValue)) {
    return 0;
  } else {
    return Math.min(100, numericValue);
  }
};

/**
 * Get user NIP from user object
 */
export const getUserNip = (user: { user_detail?: { nip?: string } | null; nip?: string }): string => {
  if (user?.user_detail && typeof user.user_detail.nip === 'string' && user.user_detail.nip.trim() !== '') {
    return user.user_detail.nip;
  }

  if (typeof user?.nip === 'string' && user.nip.trim() !== '') {
    return user.nip;
  }

  return '-';
};

/**
 * Helper function untuk normalisasi key sumber anggaran
 */
export const normalizeKey = (name: string): string => {
  const key = name.toLowerCase().replace(/\s+/g, '_');
  if (key === 'dau') return 'dak';
  if (key === 'dau_peruntukan') return 'dak_peruntukan';
  return key;
};

/**
 * Mendapatkan ID sumber anggaran dari namanya
 */
export const getSumberAnggaranId = (sumberDanaNama: string): number => {
  const normalizedName = sumberDanaNama?.toLowerCase().trim() || 'dau';
  
  const sumberDanaMapping: Record<string, number> = {
    'dau': 1,
    'dau peruntukan': 2,
    'dak fisik': 3,
    'dak non fisik': 4,
    'blud': 5,
    'dak': 1,
    'dak peruntukan': 2,
    'dak non-fisik': 4,
    'apbd': 1,
    'apbn': 2,
    'multiple': 1,
    'belum diisi': 1,
  };
  
  return sumberDanaMapping[normalizedName] || 1;
}; 