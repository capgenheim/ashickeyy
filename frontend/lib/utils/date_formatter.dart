import 'package:intl/intl.dart';

class DateFormatter {
  static String format(DateTime? date) {
    if (date == null) return '';
    // Automatically translate the UTC to the exact device's location GMT!
    final localDate = date.toLocal();
    return DateFormat('dd/MM/yyyy h a').format(localDate).toLowerCase();
  }

  static String relative(DateTime? date) {
    if (date == null) return '';
    final now = DateTime.now();
    final diff = now.difference(date);

    if (diff.inDays == 0) return 'Today';
    if (diff.inDays == 1) return 'Yesterday';
    if (diff.inDays < 7) return '${diff.inDays} days ago';
    if (diff.inDays < 30) return '${(diff.inDays / 7).floor()} weeks ago';
    if (diff.inDays < 365) return '${(diff.inDays / 30).floor()} months ago';
    return format(date);
  }
}
