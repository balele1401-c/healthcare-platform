import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_shadows.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_avatar.dart';
import '../../../../shared/widgets/app_button.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_snackbar.dart';
import '../../../../shared/widgets/app_text_field.dart';
import '../controllers/profile_controller.dart';

class EditProfileScreen extends ConsumerStatefulWidget {
  const EditProfileScreen({super.key});

  @override
  ConsumerState<EditProfileScreen> createState() => _EditProfileScreenState();
}

class _EditProfileScreenState extends ConsumerState<EditProfileScreen> {
  final _formKey = GlobalKey<FormState>();

  late TextEditingController _nameController;
  late TextEditingController _phoneController;
  late TextEditingController _heightController;
  late TextEditingController _weightController;
  late TextEditingController _addressController;
  late TextEditingController _emergencyNameController;
  late TextEditingController _emergencyPhoneController;

  late DateTime _selectedDob;
  late String _selectedGender;
  late String _selectedBloodType;

  final List<String> _bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
  final List<String> _genders = ['Female', 'Male', 'Other'];

  @override
  void initState() {
    super.initState();
    final profile = ref.read(patientProfileProvider);
    _nameController = TextEditingController(text: profile.fullName);
    _phoneController = TextEditingController(text: profile.phoneNumber);
    _heightController = TextEditingController(text: profile.heightCm);
    _weightController = TextEditingController(text: profile.weightKg);
    _addressController = TextEditingController(text: profile.address);
    _emergencyNameController = TextEditingController(text: profile.emergencyContactName);
    _emergencyPhoneController = TextEditingController(text: profile.emergencyContactPhone);

    _selectedDob = profile.dateOfBirth;
    _selectedGender = profile.gender;
    _selectedBloodType = profile.bloodType;
  }

  @override
  void dispose() {
    _nameController.dispose();
    _phoneController.dispose();
    _heightController.dispose();
    _weightController.dispose();
    _addressController.dispose();
    _emergencyNameController.dispose();
    _emergencyPhoneController.dispose();
    super.dispose();
  }

  void _handleSave() {
    if (_formKey.currentState!.validate()) {
      ref.read(patientProfileProvider.notifier).updateProfile(
            fullName: _nameController.text.trim(),
            phoneNumber: _phoneController.text.trim(),
            dateOfBirth: _selectedDob,
            gender: _selectedGender,
            bloodType: _selectedBloodType,
            heightCm: _heightController.text.trim(),
            weightKg: _weightController.text.trim(),
            address: _addressController.text.trim(),
            emergencyContactName: _emergencyNameController.text.trim(),
            emergencyContactPhone: _emergencyPhoneController.text.trim(),
          );

      AppSnackbar.showSuccess(context, 'Patient profile updated successfully.');
      Navigator.pop(context);
    }
  }

  @override
  Widget build(BuildContext context) {
    final profile = ref.watch(patientProfileProvider);

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        scrolledUnderElevation: 0.5,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded, color: AppColors.onSurface),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          'Edit Profile Details',
          style: AppTypography.titleLarge.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w800,
          ),
        ),
      ),
      body: LayoutBuilder(
        builder: (context, constraints) {
          final isDesktop = constraints.maxWidth >= 900;

          return SingleChildScrollView(
            padding: EdgeInsets.symmetric(
              horizontal: isDesktop ? AppSpacing.desktopMargin : AppSpacing.marginMobile,
              vertical: AppSpacing.lg,
            ),
            child: Center(
              child: ConstrainedBox(
                constraints: const BoxConstraints(maxWidth: 860),
                child: Form(
                  key: _formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // 1. Avatar Card
                      AppCard(
                        padding: const EdgeInsets.all(AppSpacing.lg),
                        child: Center(
                          child: Stack(
                            children: [
                              AppAvatar(
                                name: profile.fullName,
                                imageUrl: profile.avatarUrl,
                                size: 88,
                              ),
                              Positioned(
                                bottom: 0,
                                right: 0,
                                child: Container(
                                  padding: const EdgeInsets.all(6),
                                  decoration: const BoxDecoration(
                                    color: AppColors.primary,
                                    shape: BoxShape.circle,
                                  ),
                                  child: const Icon(Icons.camera_alt_rounded, color: Colors.white, size: 16),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                      AppSpacing.gapVLg,

                      // 2. Personal Information
                      Text(
                        'Personal Information',
                        style: AppTypography.titleLarge.copyWith(
                          color: AppColors.onSurface,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                      AppSpacing.gapVSm,
                      AppCard(
                        padding: const EdgeInsets.all(AppSpacing.md),
                        child: Column(
                          children: [
                            AppTextField(
                              label: 'Full Legal Name',
                              controller: _nameController,
                              prefixIcon: const Icon(Icons.person_outline_rounded, color: AppColors.outline),
                              validator: (val) => val == null || val.isEmpty ? 'Please enter your full name' : null,
                            ),
                            AppSpacing.gapVMd,
                            AppTextField(
                              label: 'Primary Phone Number',
                              controller: _phoneController,
                              keyboardType: TextInputType.phone,
                              prefixIcon: const Icon(Icons.phone_outlined, color: AppColors.outline),
                              validator: (val) => val == null || val.isEmpty ? 'Please enter phone number' : null,
                            ),
                            AppSpacing.gapVMd,
                            AppTextField(
                              label: 'Residential Street Address',
                              controller: _addressController,
                              prefixIcon: const Icon(Icons.home_outlined, color: AppColors.outline),
                              validator: (val) => val == null || val.isEmpty ? 'Please enter address' : null,
                            ),
                          ],
                        ),
                      ),
                      AppSpacing.gapVLg,

                      // 3. Clinical & Biometric Vitals
                      Text(
                        'Clinical Parameters',
                        style: AppTypography.titleLarge.copyWith(
                          color: AppColors.onSurface,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                      AppSpacing.gapVSm,
                      AppCard(
                        padding: const EdgeInsets.all(AppSpacing.md),
                        child: Column(
                          children: [
                            Row(
                              children: [
                                Expanded(
                                  child: AppTextField(
                                    label: 'Height (cm)',
                                    controller: _heightController,
                                    keyboardType: TextInputType.number,
                                    prefixIcon: const Icon(Icons.height_rounded, color: AppColors.outline),
                                  ),
                                ),
                                AppSpacing.gapHMd,
                                Expanded(
                                  child: AppTextField(
                                    label: 'Weight (kg)',
                                    controller: _weightController,
                                    keyboardType: TextInputType.number,
                                    prefixIcon: const Icon(Icons.monitor_weight_outlined, color: AppColors.outline),
                                  ),
                                ),
                              ],
                            ),
                            AppSpacing.gapVMd,
                            Row(
                              children: [
                                Expanded(
                                  child: DropdownButtonFormField<String>(
                                    initialValue: _selectedBloodType,
                                    decoration: InputDecoration(
                                      labelText: 'Blood Type',
                                      filled: true,
                                      fillColor: AppColors.surface,
                                      border: OutlineInputBorder(
                                        borderRadius: AppRadius.radiusMd,
                                        borderSide: const BorderSide(color: AppColors.outlineVariant, width: 0.8),
                                      ),
                                      enabledBorder: OutlineInputBorder(
                                        borderRadius: AppRadius.radiusMd,
                                        borderSide: const BorderSide(color: AppColors.outlineVariant, width: 0.8),
                                      ),
                                    ),
                                    items: _bloodTypes.map((type) {
                                      return DropdownMenuItem(value: type, child: Text(type));
                                    }).toList(),
                                    onChanged: (val) {
                                      if (val != null) setState(() => _selectedBloodType = val);
                                    },
                                  ),
                                ),
                                AppSpacing.gapHMd,
                                Expanded(
                                  child: DropdownButtonFormField<String>(
                                    initialValue: _selectedGender,
                                    decoration: InputDecoration(
                                      labelText: 'Gender',
                                      filled: true,
                                      fillColor: AppColors.surface,
                                      border: OutlineInputBorder(
                                        borderRadius: AppRadius.radiusMd,
                                        borderSide: const BorderSide(color: AppColors.outlineVariant, width: 0.8),
                                      ),
                                      enabledBorder: OutlineInputBorder(
                                        borderRadius: AppRadius.radiusMd,
                                        borderSide: const BorderSide(color: AppColors.outlineVariant, width: 0.8),
                                      ),
                                    ),
                                    items: _genders.map((g) {
                                      return DropdownMenuItem(value: g, child: Text(g));
                                    }).toList(),
                                    onChanged: (val) {
                                      if (val != null) setState(() => _selectedGender = val);
                                    },
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                      AppSpacing.gapVLg,

                      // 4. Emergency Contacts
                      Text(
                        'Emergency Contact',
                        style: AppTypography.titleLarge.copyWith(
                          color: AppColors.onSurface,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                      AppSpacing.gapVSm,
                      AppCard(
                        padding: const EdgeInsets.all(AppSpacing.md),
                        child: Column(
                          children: [
                            AppTextField(
                              label: 'Contact Full Name',
                              controller: _emergencyNameController,
                              prefixIcon: const Icon(Icons.emergency_outlined, color: AppColors.outline),
                            ),
                            AppSpacing.gapVMd,
                            AppTextField(
                              label: 'Emergency Phone',
                              controller: _emergencyPhoneController,
                              keyboardType: TextInputType.phone,
                              prefixIcon: const Icon(Icons.phone_outlined, color: AppColors.outline),
                            ),
                          ],
                        ),
                      ),
                      AppSpacing.gapVXxl,
                    ],
                  ),
                ),
              ),
            ),
          );
        },
      ),

      // 5. Sticky Bottom Save Bar
      bottomNavigationBar: Container(
        padding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.marginMobile,
          vertical: AppSpacing.md,
        ),
        decoration: const BoxDecoration(
          color: AppColors.surface,
          border: Border(
            top: BorderSide(color: AppColors.outlineVariant, width: 0.8),
          ),
          boxShadow: AppShadows.bottomNav,
        ),
        child: SafeArea(
          child: Center(
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 860),
              child: AppButton(
                text: 'Save Clinical Profile Changes',
                prefixIcon: Icons.save_rounded,
                onPressed: _handleSave,
              ),
            ),
          ),
        ),
      ),
    );
  }
}
