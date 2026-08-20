import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../core/utils/validators.dart';
import '../../../../shared/widgets/app_badge.dart';
import '../../../../shared/widgets/app_button.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_divider.dart';
import '../../../../shared/widgets/app_logo.dart';
import '../../../../shared/widgets/app_password_field.dart';
import '../../../../shared/widgets/app_snackbar.dart';
import '../../../../shared/widgets/app_text_button.dart';
import '../../../../shared/widgets/app_text_field.dart';
import '../../../profile/presentation/controllers/profile_controller.dart';
import '../controllers/auth_controller.dart';

class PatientLoginScreen extends ConsumerStatefulWidget {
  const PatientLoginScreen({super.key});

  @override
  ConsumerState<PatientLoginScreen> createState() => _PatientLoginScreenState();
}

class _PatientLoginScreenState extends ConsumerState<PatientLoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _isGoogleSigningIn = false;

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  Future<void> _handleLogin() async {
    if (!_formKey.currentState!.validate()) return;

    final authController = ref.read(authControllerProvider.notifier);
    final success = await authController.login(
      email: _emailController.text.trim(),
      password: _passwordController.text,
    );

    if (!mounted) return;

    if (success) {
      final user = ref.read(authControllerProvider).user;
      if (user != null && !user.isHealthProfileCompleted) {
        context.go(AppRoutes.createHealthProfile);
      } else {
        context.go(AppRoutes.home);
      }
    } else {
      final errorMessage = ref.read(authControllerProvider).errorMessage ?? 'Login failed.';
      AppSnackbar.showError(context, errorMessage);
    }
  }

  Future<void> _handleGoogleLogin() async {
    setState(() => _isGoogleSigningIn = true);

    final authController = ref.read(authControllerProvider.notifier);
    final success = await authController.signInWithGoogle();

    if (!mounted) return;
    setState(() => _isGoogleSigningIn = false);

    if (success) {
      final user = ref.read(authControllerProvider).user;
      if (user != null) {
        ref.read(patientProfileProvider.notifier).syncFromUser(user);
        if (!user.isHealthProfileCompleted) {
          context.go(AppRoutes.createHealthProfile);
        } else {
          context.go(AppRoutes.home);
        }
      } else {
        context.go(AppRoutes.home);
      }
    } else {
      final errorMessage = ref.read(authControllerProvider).errorMessage;
      if (errorMessage != null && errorMessage.isNotEmpty) {
        AppSnackbar.showError(context, errorMessage);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: LayoutBuilder(
        builder: (context, constraints) {
          if (constraints.maxWidth >= 960) {
            return _buildDesktopLayout(context);
          }
          return _buildMobileLayout(context);
        },
      ),
    );
  }

  Widget _buildDesktopLayout(BuildContext context) {
    return Row(
      children: [
        // Left Branding & Healthcare Credibility Hero Panel
        Expanded(
          flex: 6,
          child: Container(
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                colors: [Color(0xFF0F172A), Color(0xFF1E3A8A)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
            ),
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 64, vertical: 48),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  // Top Logo
                  const AppLogo(iconSize: 40, fontSize: 24, isLight: true),

                  // Middle Value Proposition
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const AppBadge(
                        text: 'SECURE CLINICAL PLATFORM',
                        variant: BadgeVariant.primary,
                      ),
                      AppSpacing.gapVMd,
                      Text(
                        'Next-Generation Integrated Healthcare for Patients and Physicians.',
                        style: AppTypography.displayLarge.copyWith(
                          color: Colors.white,
                          fontSize: 36,
                          fontWeight: FontWeight.w800,
                          height: 1.2,
                        ),
                      ),
                      AppSpacing.gapVMd,
                      Text(
                        'Schedule verified telemedicine consultations, manage electronic health records, and track vital signs in a single unified HIPAA-compliant portal.',
                        style: AppTypography.bodyLg.copyWith(
                          color: const Color(0xFFCBD5E1),
                          height: 1.5,
                        ),
                      ),
                      AppSpacing.gapVXl,

                      // Trust Badges Grid
                      Row(
                        children: [
                          _buildTrustPill(Icons.verified_user_outlined, 'HIPAA Compliant', '256-bit SSL Data Security'),
                          const SizedBox(width: 24),
                          _buildTrustPill(Icons.medical_services_outlined, 'Board-Certified', '120+ Specialist Doctors'),
                        ],
                      ),
                    ],
                  ),

                  // Footer Copyright / Status
                  Text(
                    '© 2026 HealthCare Integrated Medical Platform. All rights reserved.',
                    style: AppTypography.labelSm.copyWith(color: const Color(0xFF64748B)),
                  ),
                ],
              ),
            ),
          ),
        ),

        // Right Authentication Card Panel
        Expanded(
          flex: 5,
          child: Container(
            color: AppColors.background,
            child: Center(
              child: SingleChildScrollView(
                padding: const EdgeInsets.symmetric(horizontal: 48, vertical: 32),
                child: ConstrainedBox(
                  constraints: const BoxConstraints(maxWidth: 440),
                  child: AppCard(
                    padding: const EdgeInsets.all(AppSpacing.xl),
                    child: _buildLoginForm(context),
                  ),
                ),
              ),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildTrustPill(IconData icon, String title, String subtitle) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(10),
          decoration: BoxDecoration(
            color: Colors.white.withValues(alpha: 0.08),
            borderRadius: AppRadius.radiusMd,
          ),
          child: Icon(icon, color: AppColors.secondaryLight, size: 20),
        ),
        AppSpacing.gapHSm,
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              title,
              style: AppTypography.labelMd.copyWith(
                color: Colors.white,
                fontWeight: FontWeight.w700,
              ),
            ),
            Text(
              subtitle,
              style: AppTypography.labelSm.copyWith(
                color: const Color(0xFF94A3B8),
                fontSize: 11,
              ),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildMobileLayout(BuildContext context) {
    return SafeArea(
      child: Center(
        child: SingleChildScrollView(
          padding: AppSpacing.paddingScreenAll,
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 440),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const AppLogo(iconSize: 44, fontSize: 24),
                AppSpacing.gapVXl,
                AppCard(
                  padding: const EdgeInsets.all(AppSpacing.xl),
                  child: _buildLoginForm(context),
                ),
                AppSpacing.gapVLg,
                _buildRegisterLink(context),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildLoginForm(BuildContext context) {
    final authState = ref.watch(authControllerProvider);

    return Form(
      key: _formKey,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        mainAxisSize: MainAxisSize.min,
        children: [
          Text(
            'Welcome Back',
            style: AppTypography.headlineSm.copyWith(
              color: AppColors.onSurface,
              fontWeight: FontWeight.w800,
            ),
          ),
          AppSpacing.gapVXs,
          Text(
            'Sign in to access your clinical dashboard and medical records.',
            style: AppTypography.bodySm.copyWith(
              color: AppColors.onSurfaceVariant,
            ),
          ),
          AppSpacing.gapVLg,

          AppTextField(
            label: 'Email Address',
            hintText: 'Enter your email address',
            controller: _emailController,
            keyboardType: TextInputType.emailAddress,
            validator: Validators.validateEmail,
            prefixIcon: const Icon(Icons.mail_outline_rounded, size: 20, color: AppColors.outline),
          ),
          AppSpacing.gapVMd,

          AppPasswordField(
            label: 'Password',
            hintText: 'Enter your password',
            controller: _passwordController,
            validator: Validators.validatePassword,
          ),
          AppSpacing.gapVSm,

          Align(
            alignment: Alignment.centerRight,
            child: AppTextButton(
              text: 'Forgot Password?',
              onPressed: () => context.push(AppRoutes.forgotPassword),
            ),
          ),
          AppSpacing.gapVLg,

          AppButton(
            text: 'Sign In',
            isLoading: authState.isLoading,
            onPressed: _handleLogin,
          ),

          const AppDivider(text: 'OR SIGN IN WITH'),

          AppButton(
            text: 'Continue with Google',
            variant: ButtonVariant.outlined,
            prefixWidget: SvgPicture.asset(
              'assets/icons/google_logo.svg',
              width: 20,
              height: 20,
            ),
            isLoading: _isGoogleSigningIn,
            onPressed: (authState.isLoading || _isGoogleSigningIn)
                ? null
                : _handleGoogleLogin,
          ),

          if (MediaQuery.of(context).size.width >= 960) ...[
            AppSpacing.gapVXl,
            _buildRegisterLink(context),
          ]
        ],
      ),
    );
  }

  Widget _buildRegisterLink(BuildContext context) {
    return Wrap(
      alignment: WrapAlignment.center,
      crossAxisAlignment: WrapCrossAlignment.center,
      children: [
        Text(
          "Don't have an account? ",
          style: AppTypography.bodyMd.copyWith(color: AppColors.onSurfaceVariant),
        ),
        AppTextButton(
          text: 'Create an account',
          onPressed: () => context.push(AppRoutes.register),
        ),
      ],
    );
  }
}
