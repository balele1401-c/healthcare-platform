import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_sign_in/google_sign_in.dart';
import '../errors/failures.dart';

final firebaseAuthServiceProvider = Provider<FirebaseAuthService>((ref) {
  return FirebaseAuthService();
});

/// Service responsible for Firebase Authentication and Google Sign-In operations.
class FirebaseAuthService {
  final FirebaseAuth? _customAuth;
  final GoogleSignIn? _customGoogleSignIn;

  FirebaseAuthService({
    FirebaseAuth? firebaseAuth,
    GoogleSignIn? googleSignIn,
  })  : _customAuth = firebaseAuth,
        _customGoogleSignIn = googleSignIn;

  FirebaseAuth? get _firebaseAuth {
    if (_customAuth != null) return _customAuth;
    try {
      return FirebaseAuth.instance;
    } catch (_) {
      return null;
    }
  }

  GoogleSignIn get _googleSignIn {
    return _customGoogleSignIn ??
        GoogleSignIn(
          scopes: const [
            'email',
            'profile',
          ],
        );
  }

  /// Current authenticated Firebase user.
  User? get currentUser {
    try {
      return _firebaseAuth?.currentUser;
    } catch (_) {
      return null;
    }
  }

  /// Stream of authentication state changes.
  Stream<User?> get authStateChanges {
    final auth = _firebaseAuth;
    if (auth == null) {
      return Stream.value(null);
    }
    return auth.authStateChanges();
  }

  /// Authenticate with Google Sign-In and link with Firebase.
  /// Returns [UserCredential] on success, or `null` if the user cancels.
  Future<UserCredential?> signInWithGoogle() async {
    final auth = _firebaseAuth;
    if (auth == null) {
      throw const AuthFailure('Firebase is not initialized.');
    }

    try {
      if (kIsWeb) {
        // Web flow using Firebase Auth GoogleAuthProvider popup
        final GoogleAuthProvider googleProvider = GoogleAuthProvider();
        googleProvider.addScope('email');
        googleProvider.addScope('profile');
        googleProvider.setCustomParameters({'prompt': 'select_account'});
        return await auth.signInWithPopup(googleProvider);
      } else {
        // Android / iOS / Native platform flow
        final GoogleSignInAccount? googleUser = await _googleSignIn.signIn();
        if (googleUser == null) {
          // User closed or cancelled the account picker
          return null;
        }

        final GoogleSignInAuthentication googleAuth = await googleUser.authentication;
        final OAuthCredential credential = GoogleAuthProvider.credential(
          accessToken: googleAuth.accessToken,
          idToken: googleAuth.idToken,
        );

        return await auth.signInWithCredential(credential);
      }
    } on FirebaseAuthException catch (e) {
      if (e.code == 'popup-closed-by-user' || e.code == 'canceled' || e.code == 'cancelled') {
        return null;
      }
      throw AuthFailure(_mapFirebaseErrorMessage(e), e.code);
    } catch (e) {
      if (e.toString().contains('canceled') || e.toString().contains('cancelled') || e.toString().contains('popup-closed')) {
        return null;
      }
      throw AuthFailure('Failed to sign in with Google: ${e.toString()}');
    }
  }

  /// Sign out from both Firebase and Google Sign-In.
  Future<void> signOut() async {
    try {
      await _firebaseAuth?.signOut();
    } catch (_) {}

    try {
      if (!kIsWeb) {
        await _googleSignIn.signOut();
      }
    } catch (_) {}
  }

  /// Map Firebase error codes to user-friendly messages.
  String _mapFirebaseErrorMessage(FirebaseAuthException e) {
    switch (e.code) {
      case 'account-exists-with-different-credential':
        return 'An account already exists with the same email address using another sign-in method.';
      case 'invalid-credential':
        return 'The authentication credential is invalid or has expired.';
      case 'operation-not-allowed':
        return 'Google Sign-In is not enabled in Firebase Console. Please contact support.';
      case 'user-disabled':
        return 'This account has been disabled. Please contact your system administrator.';
      case 'user-not-found':
        return 'No user found corresponding to this credential.';
      case 'network-request-failed':
        return 'Network connection failed. Please check your internet connection and try again.';
      case 'popup-blocked':
        return 'The sign-in popup was blocked by your browser. Please allow popups for this site.';
      default:
        return e.message ?? 'An error occurred during authentication. Please try again.';
    }
  }
}
