import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../constants/app_constants.dart';

/// Service managing secure keychain/keystore tokens and shared preferences.
class SecureStorageService {
  final FlutterSecureStorage _secureStorage;
  SharedPreferences? _prefs;

  SecureStorageService({
    FlutterSecureStorage? secureStorage,
  }) : _secureStorage = secureStorage ??
            const FlutterSecureStorage(
              aOptions: AndroidOptions(
                encryptedSharedPreferences: true,
              ),
              iOptions: IOSOptions(
                accessibility: KeychainAccessibility.first_unlock,
              ),
            );

  Future<void> init() async {
    _prefs ??= await SharedPreferences.getInstance();
  }

  // Token Management (Secure Storage)
  Future<void> saveToken(String token) async {
    await _secureStorage.write(key: AppConstants.tokenKey, value: token);
  }

  Future<String?> getToken() async {
    return await _secureStorage.read(key: AppConstants.tokenKey);
  }

  Future<void> deleteToken() async {
    await _secureStorage.delete(key: AppConstants.tokenKey);
  }

  Future<void> saveRefreshToken(String token) async {
    await _secureStorage.write(key: AppConstants.refreshTokenKey, value: token);
  }

  Future<String?> getRefreshToken() async {
    return await _secureStorage.read(key: AppConstants.refreshTokenKey);
  }

  // Onboarding Management (Shared Preferences)
  Future<void> setOnboardingCompleted(bool completed) async {
    await init();
    await _prefs?.setBool(AppConstants.onboardingCompletedKey, completed);
  }

  Future<bool> isOnboardingCompleted() async {
    await init();
    return _prefs?.getBool(AppConstants.onboardingCompletedKey) ?? false;
  }

  // Custom Storage Values
  Future<void> saveCustomValue(String key, String value) async {
    await _secureStorage.write(key: key, value: value);
  }

  Future<String?> getCustomValue(String key) async {
    return await _secureStorage.read(key: key);
  }

  Future<void> deleteCustomValue(String key) async {
    await _secureStorage.delete(key: key);
  }

  // Clear Session
  Future<void> clearSession() async {
    await _secureStorage.delete(key: AppConstants.tokenKey);
    await _secureStorage.delete(key: AppConstants.refreshTokenKey);
    await _secureStorage.delete(key: AppConstants.userKey);
  }
}
