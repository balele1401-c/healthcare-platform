import 'package:intl/intl.dart';

/// Clinical date and time formatting utilities.
abstract class DateFormatter {
  static final DateFormat _displayDate = DateFormat('MMM dd, yyyy');
  static final DateFormat _fullDate = DateFormat('EEEE, MMMM d, yyyy');
  static final DateFormat _shortDate = DateFormat('dd/MM/yyyy');
  static final DateFormat _timeOnly = DateFormat('hh:mm a');
  static final DateFormat _isoDate = DateFormat('yyyy-MM-dd');

  static String formatDate(DateTime? date) {
    if (date == null) return '-';
    return _displayDate.format(date);
  }

  static String formatFullDate(DateTime? date) {
    if (date == null) return '-';
    return _fullDate.format(date);
  }

  static String formatShortDate(DateTime? date) {
    if (date == null) return '-';
    return _shortDate.format(date);
  }

  static String formatTime(DateTime? date) {
    if (date == null) return '-';
    return _timeOnly.format(date);
  }

  static String toIsoDateString(DateTime date) {
    return _isoDate.format(date);
  }

  static DateTime? parseIsoDate(String? dateString) {
    if (dateString == null || dateString.isEmpty) return null;
    try {
      return DateTime.parse(dateString);
    } catch (_) {
      return null;
    }
  }
}
