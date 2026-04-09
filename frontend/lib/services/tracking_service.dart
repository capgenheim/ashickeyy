// ignore_for_file: avoid_web_libraries_in_flutter

import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:geolocator/geolocator.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:uuid/uuid.dart';
import 'dart:html' as html;
import 'dart:js' as js;

class TrackingService {
  static const String baseUrl = 'http://localhost:8080/api/tracking';
  static const String publicVapidKey = 'BJgc7yMNMJQXLuw7LulYHUF7KQqzor8-lmnjInJsf_7N5MmgS8hpVCC5gUAZ2n9kgIwnGJV4Ex937XUzf9IrHxg';

  static Future<void> initializeTracking({String? postSlug}) async {
    try {
      // 1. Geolocation Tracking
      bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
      LocationPermission permission = await Geolocator.checkPermission();
      
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
      }

      double? lat;
      double? lng;

      if (permission == LocationPermission.whileInUse || permission == LocationPermission.always) {
        Position position = await Geolocator.getCurrentPosition();
        lat = position.latitude;
        lng = position.longitude;
      }

      // 2. Resolve persistent Visitor ID
      final prefs = await SharedPreferences.getInstance();
      String? visitorId = prefs.getString('ashickey_visitor_id');
      if (visitorId == null) {
        visitorId = const Uuid().v4();
        await prefs.setString('ashickey_visitor_id', visitorId);
      }

      // Log the payload to backend
      http.post(
        Uri.parse('$baseUrl/analytics'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'visitorId': visitorId,
          'postSlug': postSlug,
          'latitude': lat,
          'longitude': lng,
          'userAgent': html.window.navigator.userAgent,
        }),
      ).catchError((_) { /* ignore */ });

      // 3. Web Push Notification Handshake
      if (html.Notification.permission != 'granted') {
        final permissionResult = await html.Notification.requestPermission();
        if (permissionResult == 'granted') {
          await _subscribeToPush();
        }
      } else {
        await _subscribeToPush();
      }

    } catch (e) {
      print('Tracking init error: $e');
    }
  }

  static Future<void> _subscribeToPush() async {
    try {
      js.context.callMethod('registerWebPush', [publicVapidKey, '$baseUrl/subscribe']);
    } catch (e) {
      print('Push subscription error: $e');
    }
  }
}
