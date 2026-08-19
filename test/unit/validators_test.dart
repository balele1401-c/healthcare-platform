import 'package:flutter_test/flutter_test.dart';
import 'package:healthcare/core/utils/validators.dart';

void main() {
  group('Validators Test Suite', () {
    test('validateEmail validates correctly', () {
      expect(Validators.validateEmail(''), isNotNull);
      expect(Validators.validateEmail('invalid'), isNotNull);
      expect(Validators.validateEmail('sarah@'), isNotNull);
      expect(Validators.validateEmail('sarah.jenkins@example.com'), isNull);
    });

    test('validatePassword validates length constraint', () {
      expect(Validators.validatePassword(''), isNotNull);
      expect(Validators.validatePassword('12345'), isNotNull);
      expect(Validators.validatePassword('Password123!'), isNull);
    });

    test('validateConfirmPassword ensures password match', () {
      expect(Validators.validateConfirmPassword('Password123!', 'Different!'), isNotNull);
      expect(Validators.validateConfirmPassword('Password123!', 'Password123!'), isNull);
    });

    test('validateName validates minimum length', () {
      expect(Validators.validateName(''), isNotNull);
      expect(Validators.validateName('A'), isNotNull);
      expect(Validators.validateName('Sarah Jenkins'), isNull);
    });

    test('validatePhone validates valid digits', () {
      expect(Validators.validatePhone(''), isNotNull);
      expect(Validators.validatePhone('123'), isNotNull);
      expect(Validators.validatePhone('+15550192834'), isNull);
      expect(Validators.validatePhone('08123456789'), isNull);
    });

    test('validateOtp validates 6-digit numeric input', () {
      expect(Validators.validateOtp(''), isNotNull);
      expect(Validators.validateOtp('12345'), isNotNull);
      expect(Validators.validateOtp('12345a'), isNotNull);
      expect(Validators.validateOtp('123456'), isNull);
    });

    test('validateTerms ensures agreement checkbox', () {
      expect(Validators.validateTerms(false), isNotNull);
      expect(Validators.validateTerms(null), isNotNull);
      expect(Validators.validateTerms(true), isNull);
    });
  });
}
