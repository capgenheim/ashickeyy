import 'package:flutter_test/flutter_test.dart';
import 'package:ashickeyy/utils/date_formatter.dart';

void main() {
  group('DateFormatter', () {
    test('format properly shifts external UTC to local boundaries without crashing', () {
      // Create an explicit mock UTC Date payload tracking standard ISO formats shipped by Laravel
      final utcDate = DateTime.utc(2026, 4, 9, 11, 0, 0); // 11:00 AM UTC
      
      // Calculate explicitly locally
      final formattedString = DateFormatter.format(utcDate);

      // Verify strings dynamically map safely into our standardized output!
      expect(formattedString.contains('09/04/2026'), isTrue);
    });

    test('format returns empty string firmly on null injection gracefully', () {
      expect(DateFormatter.format(null), equals(''));
      expect(DateFormatter.relative(null), equals(''));
    });
  });
}
