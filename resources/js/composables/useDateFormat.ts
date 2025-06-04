export function useDateFormat() {
  const parseLocalDate = (dateStr: string): Date => {
    const [year, month, day] = dateStr.split('-').map(Number);
    return new Date(year, month - 1, day); // Month is 0-based
  };

  const formatDate = (dateString: string | null | undefined): string => {
    if (!dateString) return 'Not assigned';

    try {
      const date = parseLocalDate(dateString);
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    } catch {
      return 'Invalid date';
    }
  };

  return {
    formatDate,
    parseLocalDate
  };
}
