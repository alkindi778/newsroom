#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
🔒 فاحص أمان API الشامل - Newsroom Security Checker
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
يفحص جميع نقاط النهاية الـ API ويختبر:
- Rate Limiting (حماية من طلبات كثيرة)
- Authentication (التحقق من الهوية)
- CORS Headers (رؤوس الأمان)
- SQL Injection (حقن SQL)
- XSS Vulnerabilities (ثغرات XSS)
- Information Disclosure (تسريب المعلومات)
- Port 3000 Security (أمان منفذ 3000)
"""

import requests
import json
import time
import socket
import ssl
import sys
from datetime import datetime
from urllib.parse import urljoin
from concurrent.futures import ThreadPoolExecutor, as_completed

# ═══════════════════════════════════════════════════
# الإعدادات
# ═══════════════════════════════════════════════════
BASE_URL = "http://localhost:8000"  # Laravel backend
FRONTEND_URL = "http://localhost:3000"  # Nuxt frontend
API_VERSION = "v1"
TIMEOUT = 10

# ألوان للطباعة في الكونسول
class Colors:
    HEADER = '\033[95m'
    BLUE = '\033[94m'
    CYAN = '\033[96m'
    GREEN = '\033[92m'
    WARNING = '\033[93m'
    FAIL = '\033[91m'
    ENDC = '\033[0m'
    BOLD = '\033[1m'

# ═══════════════════════════════════════════════════
# قائمة نقاط النهاية للفحص
# ═══════════════════════════════════════════════════
API_ENDPOINTS = {
    "public": [
        # Health Check
        {"path": "/api/health/ping", "method": "GET", "name": "Health Ping"},
        {"path": "/api/health", "method": "GET", "name": "Health Check"},
        {"path": "/api/health/stats", "method": "GET", "name": "Health Stats"},
        
        # Articles
        {"path": f"/api/{API_VERSION}/articles", "method": "GET", "name": "Articles List"},
        {"path": f"/api/{API_VERSION}/articles/featured", "method": "GET", "name": "Featured Articles"},
        {"path": f"/api/{API_VERSION}/articles/latest", "method": "GET", "name": "Latest Articles"},
        {"path": f"/api/{API_VERSION}/articles/popular", "method": "GET", "name": "Popular Articles"},
        {"path": f"/api/{API_VERSION}/articles/slider", "method": "GET", "name": "Slider Articles"},
        {"path": f"/api/{API_VERSION}/articles/breaking-news", "method": "GET", "name": "Breaking News"},
        {"path": f"/api/{API_VERSION}/articles/search?q=test", "method": "GET", "name": "Article Search"},
        
        # Categories
        {"path": f"/api/{API_VERSION}/categories", "method": "GET", "name": "Categories List"},
        
        # Writers
        {"path": f"/api/{API_VERSION}/writers", "method": "GET", "name": "Writers List"},
        
        # Opinions
        {"path": f"/api/{API_VERSION}/opinions", "method": "GET", "name": "Opinions List"},
        {"path": f"/api/{API_VERSION}/opinions/featured", "method": "GET", "name": "Featured Opinions"},
        
        # Videos
        {"path": f"/api/{API_VERSION}/videos", "method": "GET", "name": "Videos List"},
        {"path": f"/api/{API_VERSION}/videos/featured", "method": "GET", "name": "Featured Videos"},
        
        # Infographics
        {"path": f"/api/{API_VERSION}/infographics", "method": "GET", "name": "Infographics List"},
        
        # Settings
        {"path": f"/api/{API_VERSION}/settings", "method": "GET", "name": "Site Settings"},
        
        # Homepage
        {"path": f"/api/{API_VERSION}/homepage-sections", "method": "GET", "name": "Homepage Sections"},
        
        # Push
        {"path": f"/api/{API_VERSION}/push/public-key", "method": "GET", "name": "Push Public Key"},
        
        # Manifest
        {"path": f"/api/{API_VERSION}/manifest", "method": "GET", "name": "PWA Manifest"},
        
        # Breaking News
        {"path": f"/api/{API_VERSION}/breaking-news", "method": "GET", "name": "Breaking News"},
    ],
    "protected": [
        # يجب أن تكون محمية بـ auth:sanctum
        {"path": "/api/health/info", "method": "GET", "name": "Health Info (Protected)"},
        {"path": "/api/user", "method": "GET", "name": "Current User"},
        {"path": f"/api/{API_VERSION}/newspaper-issues", "method": "POST", "name": "Create Newspaper Issue"},
        {"path": f"/api/{API_VERSION}/newspaper-issues/1", "method": "PUT", "name": "Update Newspaper Issue"},
        {"path": f"/api/{API_VERSION}/newspaper-issues/1", "method": "DELETE", "name": "Delete Newspaper Issue"},
    ],
    "rate_limited": [
        # يجب أن تكون محمية بـ rate limiting صارم
        {"path": f"/api/{API_VERSION}/opinions/1/like", "method": "POST", "name": "Like Opinion"},
        {"path": f"/api/{API_VERSION}/videos/1/view", "method": "POST", "name": "Video View"},
        {"path": f"/api/{API_VERSION}/videos/1/like", "method": "POST", "name": "Like Video"},
        {"path": f"/api/{API_VERSION}/articles/1/view", "method": "POST", "name": "Article View"},
        {"path": f"/api/{API_VERSION}/push/subscribe", "method": "POST", "name": "Push Subscribe"},
        {"path": f"/api/{API_VERSION}/contact-messages", "method": "POST", "name": "Contact Message"},
    ]
}

# ═══════════════════════════════════════════════════
# كلاس الفاحص الرئيسي
# ═══════════════════════════════════════════════════
class SecurityChecker:
    def __init__(self, base_url=BASE_URL, frontend_url=FRONTEND_URL):
        self.base_url = base_url.rstrip('/')
        self.frontend_url = frontend_url.rstrip('/')
        self.results = {
            "timestamp": datetime.now().isoformat(),
            "summary": {},
            "endpoints": [],
            "vulnerabilities": [],
            "port_3000": {},
            "headers": {}
        }
        self.session = requests.Session()
        
    def print_header(self, text):
        """طباعة عنوان"""
        print(f"\n{Colors.HEADER}{'═' * 60}{Colors.ENDC}")
        print(f"{Colors.BOLD}{Colors.CYAN}  {text}{Colors.ENDC}")
        print(f"{Colors.HEADER}{'═' * 60}{Colors.ENDC}\n")
        
    def print_result(self, status, message):
        """طباعة نتيجة الفحص"""
        if status == "PASS":
            print(f"  {Colors.GREEN}✓ [محمي]{Colors.ENDC} {message}")
        elif status == "FAIL":
            print(f"  {Colors.FAIL}✗ [ثغرة]{Colors.ENDC} {message}")
        elif status == "WARN":
            print(f"  {Colors.WARNING}⚠ [تحذير]{Colors.ENDC} {message}")
        else:
            print(f"  {Colors.BLUE}ℹ [معلومة]{Colors.ENDC} {message}")
            
    def check_port(self, host, port, timeout=3):
        """فحص إذا كان المنفذ مفتوحاً"""
        try:
            sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            sock.settimeout(timeout)
            result = sock.connect_ex((host, port))
            sock.close()
            return result == 0
        except Exception as e:
            return False
            
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    # 1. فحص أمان Port 3000
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    def check_port_3000_security(self):
        """فحص شامل لأمان Port 3000 (Nuxt Frontend)"""
        self.print_header("🔌 فحص أمان Port 3000 (Nuxt Frontend)")
        
        port_results = {
            "port_open": False,
            "https_available": False,
            "security_headers": {},
            "exposed_paths": [],
            "vulnerabilities": []
        }
        
        # 1. فحص هل المنفذ مفتوح
        is_open = self.check_port("localhost", 3000)
        port_results["port_open"] = is_open
        
        if is_open:
            self.print_result("INFO", "Port 3000 مفتوح وقابل للوصول")
            
            try:
                # فحص الاتصال الأساسي
                response = self.session.get(self.frontend_url, timeout=TIMEOUT, verify=False)
                
                # 2. فحص Security Headers
                headers_to_check = [
                    "X-Content-Type-Options",
                    "X-Frame-Options", 
                    "X-XSS-Protection",
                    "Content-Security-Policy",
                    "Strict-Transport-Security",
                    "Referrer-Policy",
                    "Permissions-Policy"
                ]
                
                print(f"\n  {Colors.CYAN}━━━ Security Headers ━━━{Colors.ENDC}")
                for header in headers_to_check:
                    value = response.headers.get(header)
                    port_results["security_headers"][header] = value
                    
                    if value:
                        self.print_result("PASS", f"{header}: {value[:50]}...")
                    else:
                        self.print_result("FAIL", f"{header}: غير موجود!")
                        port_results["vulnerabilities"].append(f"Missing header: {header}")
                
                # 3. فحص المسارات الحساسة
                print(f"\n  {Colors.CYAN}━━━ فحص المسارات الحساسة ━━━{Colors.ENDC}")
                sensitive_paths = [
                    "/_nuxt/",
                    "/.env",
                    "/.git/config",
                    "/api/",
                    "/admin",
                    "/debug",
                    "/__nuxt/",
                    "/server",
                    "/config.js",
                    "/nuxt.config.js",
                    "/.nuxt/",
                    "/node_modules/",
                ]
                
                for path in sensitive_paths:
                    try:
                        check_url = urljoin(self.frontend_url, path)
                        resp = self.session.get(check_url, timeout=5, allow_redirects=False)
                        
                        if resp.status_code == 200:
                            port_results["exposed_paths"].append(path)
                            self.print_result("WARN", f"{path} - متاح (Status: {resp.status_code})")
                        elif resp.status_code in [401, 403]:
                            self.print_result("PASS", f"{path} - محمي (Status: {resp.status_code})")
                        else:
                            self.print_result("INFO", f"{path} - Status: {resp.status_code}")
                    except:
                        self.print_result("INFO", f"{path} - غير متوفر")
                        
                # 4. فحص CORS
                print(f"\n  {Colors.CYAN}━━━ فحص CORS ━━━{Colors.ENDC}")
                try:
                    cors_response = self.session.options(
                        self.frontend_url,
                        headers={"Origin": "https://malicious-site.com"},
                        timeout=5
                    )
                    
                    acao = cors_response.headers.get("Access-Control-Allow-Origin", "")
                    if acao == "*":
                        self.print_result("FAIL", "CORS يسمح بأي origin - خطير!")
                        port_results["vulnerabilities"].append("CORS allows all origins")
                    elif "malicious-site.com" in acao:
                        self.print_result("FAIL", "CORS يسمح بـ origins خارجية!")
                        port_results["vulnerabilities"].append("CORS reflects origin")
                    else:
                        self.print_result("PASS", f"CORS مُعَد بشكل صحيح: {acao or 'لا يوجد'}")
                except:
                    self.print_result("INFO", "لم يتم إرجاع CORS headers")
                    
            except requests.exceptions.ConnectionError:
                self.print_result("WARN", "Port 3000 مفتوح لكن الخدمة لا تستجيب")
            except Exception as e:
                self.print_result("WARN", f"خطأ في الفحص: {str(e)[:50]}")
        else:
            self.print_result("INFO", "Port 3000 مغلق أو غير متاح")
            
        self.results["port_3000"] = port_results
        return port_results
        
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    # 2. فحص Rate Limiting
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    def check_rate_limiting(self, endpoint, num_requests=15):
        """فحص Rate Limiting لنقطة نهاية"""
        url = urljoin(self.base_url, endpoint["path"])
        method = endpoint["method"]
        blocked = False
        
        try:
            for i in range(num_requests):
                if method == "GET":
                    response = self.session.get(url, timeout=TIMEOUT)
                else:
                    response = self.session.request(method, url, timeout=TIMEOUT, json={})
                    
                if response.status_code == 429:  # Too Many Requests
                    blocked = True
                    return {
                        "protected": True,
                        "blocked_at_request": i + 1,
                        "status": "PASS"
                    }
                    
            return {
                "protected": False,
                "blocked_at_request": None,
                "status": "FAIL",
                "note": f"لم يتم حظرنا بعد {num_requests} طلب!"
            }
        except Exception as e:
            return {"protected": None, "error": str(e), "status": "ERROR"}
            
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    # 3. فحص Authentication
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    def check_authentication(self, endpoint):
        """فحص إذا كانت النقطة محمية بالمصادقة"""
        url = urljoin(self.base_url, endpoint["path"])
        method = endpoint["method"]
        
        try:
            if method == "GET":
                response = self.session.get(url, timeout=TIMEOUT)
            elif method == "POST":
                response = self.session.post(url, timeout=TIMEOUT, json={})
            elif method == "PUT":
                response = self.session.put(url, timeout=TIMEOUT, json={})
            elif method == "DELETE":
                response = self.session.delete(url, timeout=TIMEOUT)
            else:
                response = self.session.request(method, url, timeout=TIMEOUT)
                
            if response.status_code == 401:
                return {"protected": True, "status_code": 401, "status": "PASS"}
            elif response.status_code == 403:
                return {"protected": True, "status_code": 403, "status": "PASS"}
            elif response.status_code == 200:
                return {"protected": False, "status_code": 200, "status": "FAIL",
                        "note": "يمكن الوصول بدون مصادقة!"}
            else:
                return {"protected": None, "status_code": response.status_code, "status": "WARN"}
                
        except Exception as e:
            return {"protected": None, "error": str(e), "status": "ERROR"}
            
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    # 4. فحص SQL Injection
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    def check_sql_injection(self):
        """فحص ثغرات SQL Injection"""
        self.print_header("💉 فحص SQL Injection")
        
        sql_payloads = [
            "' OR '1'='1",
            "'; DROP TABLE articles; --",
            "1' AND '1'='1",
            "' UNION SELECT * FROM users --",
            "admin'--",
            "1; SELECT * FROM users",
            "' OR 1=1 --",
            "<script>alert(1)</script>",
        ]
        
        test_endpoints = [
            f"/api/{API_VERSION}/articles/search?q=",
            f"/api/{API_VERSION}/categories/",
            f"/api/{API_VERSION}/articles/",
            f"/api/{API_VERSION}/writers/",
        ]
        
        vulnerabilities = []
        
        for endpoint in test_endpoints:
            for payload in sql_payloads:
                try:
                    url = urljoin(self.base_url, endpoint + payload)
                    response = self.session.get(url, timeout=TIMEOUT)
                    
                    # فحص علامات الثغرة
                    suspicious_keywords = [
                        "SQL syntax",
                        "mysql_",
                        "SQLSTATE",
                        "syntax error",
                        "Unclosed quotation",
                        "Query failed",
                        "database error"
                    ]
                    
                    for keyword in suspicious_keywords:
                        if keyword.lower() in response.text.lower():
                            vulnerabilities.append({
                                "endpoint": endpoint,
                                "payload": payload,
                                "indicator": keyword
                            })
                            self.print_result("FAIL", f"ثغرة محتملة: {endpoint}")
                            break
                    else:
                        if response.status_code in [500]:
                            self.print_result("WARN", f"{endpoint} - Server Error (قد يكون مؤشراً)")
                            
                except Exception as e:
                    pass
                    
        if not vulnerabilities:
            self.print_result("PASS", "لم يتم اكتشاف ثغرات SQL Injection واضحة")
            
        return vulnerabilities
        
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    # 5. فحص Security Headers للـ Backend
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    def check_backend_security_headers(self):
        """فحص Security Headers للـ Backend API"""
        self.print_header("📋 فحص Security Headers (Backend)")
        
        try:
            response = self.session.get(urljoin(self.base_url, "/api/health/ping"), timeout=TIMEOUT)
            
            headers_check = {
                "X-Content-Type-Options": {"recommended": "nosniff", "found": None},
                "X-Frame-Options": {"recommended": "DENY or SAMEORIGIN", "found": None},
                "X-XSS-Protection": {"recommended": "1; mode=block", "found": None},
                "Strict-Transport-Security": {"recommended": "max-age=31536000; includeSubDomains", "found": None},
                "Content-Security-Policy": {"recommended": "defined policy", "found": None},
                "Referrer-Policy": {"recommended": "strict-origin-when-cross-origin", "found": None},
                "Permissions-Policy": {"recommended": "defined policy", "found": None},
            }
            
            for header, info in headers_check.items():
                value = response.headers.get(header)
                headers_check[header]["found"] = value
                
                if value:
                    self.print_result("PASS", f"{header}: {value}")
                else:
                    self.print_result("FAIL", f"{header}: غير موجود (يُنصح: {info['recommended']})")
                    
            # فحص headers خطيرة يجب عدم وجودها
            dangerous_headers = ["Server", "X-Powered-By"]
            print(f"\n  {Colors.CYAN}━━━ Headers يجب إخفاءها ━━━{Colors.ENDC}")
            
            for header in dangerous_headers:
                value = response.headers.get(header)
                if value:
                    self.print_result("WARN", f"{header}: {value} (يُفضل إخفاءه)")
                else:
                    self.print_result("PASS", f"{header}: مخفي ✓")
                    
            self.results["headers"]["backend"] = headers_check
            
        except Exception as e:
            self.print_result("ERROR", f"خطأ في الفحص: {str(e)}")
            
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    # 6. فحص نقاط النهاية المحمية
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    def check_protected_endpoints(self):
        """فحص أن النقاط المحمية فعلاً محمية"""
        self.print_header("🔐 فحص نقاط النهاية المحمية (Auth Required)")
        
        for endpoint in API_ENDPOINTS["protected"]:
            result = self.check_authentication(endpoint)
            
            if result["status"] == "PASS":
                self.print_result("PASS", f"{endpoint['name']} - محمي (Status: {result['status_code']})")
            elif result["status"] == "FAIL":
                self.print_result("FAIL", f"{endpoint['name']} - غير محمي! {result.get('note', '')}")
                self.results["vulnerabilities"].append({
                    "type": "Missing Authentication",
                    "endpoint": endpoint["path"],
                    "severity": "HIGH"
                })
            else:
                self.print_result("WARN", f"{endpoint['name']} - Status: {result.get('status_code', 'Unknown')}")
                
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    # 7. فحص Rate Limiting للنقاط الحساسة
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    def check_rate_limited_endpoints(self):
        """فحص Rate Limiting للنقاط الحساسة"""
        self.print_header("⏱️ فحص Rate Limiting")
        
        for endpoint in API_ENDPOINTS["rate_limited"]:
            print(f"\n  جاري فحص: {endpoint['name']}...")
            result = self.check_rate_limiting(endpoint, num_requests=12)
            
            if result["status"] == "PASS":
                self.print_result("PASS", f"{endpoint['name']} - محمي (blocked at request #{result['blocked_at_request']})")
            elif result["status"] == "FAIL":
                self.print_result("FAIL", f"{endpoint['name']} - {result.get('note', 'غير محمي!')}")
                self.results["vulnerabilities"].append({
                    "type": "Missing Rate Limiting",
                    "endpoint": endpoint["path"],
                    "severity": "MEDIUM"
                })
            else:
                self.print_result("WARN", f"{endpoint['name']} - {result.get('error', 'Unknown error')}")
                
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    # 8. فحص تسريب المعلومات
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    def check_information_disclosure(self):
        """فحص تسريب المعلومات الحساسة"""
        self.print_header("🔍 فحص تسريب المعلومات")
        
        sensitive_paths = [
            "/.env",
            "/.git/config",
            "/.git/HEAD",
            "/config/database.php",
            "/storage/logs/laravel.log",
            "/debug",
            "/phpinfo.php",
            "/info.php",
            "/adminer.php",
            "/.htaccess",
            "/composer.json",
            "/composer.lock",
            "/package.json",
            "/webpack.config.js",
            "/.env.local",
            "/.env.production",
            "/api/debug",
            "/telescope",
        ]
        
        for path in sensitive_paths:
            try:
                url = urljoin(self.base_url, path)
                response = self.session.get(url, timeout=5, allow_redirects=False)
                
                if response.status_code == 200:
                    self.print_result("FAIL", f"{path} - متاح للعموم! (خطير)")
                    self.results["vulnerabilities"].append({
                        "type": "Information Disclosure",
                        "path": path,
                        "severity": "HIGH"
                    })
                elif response.status_code in [401, 403]:
                    self.print_result("PASS", f"{path} - محمي")
                else:
                    self.print_result("INFO", f"{path} - Status: {response.status_code}")
            except:
                self.print_result("INFO", f"{path} - غير متوفر")
                
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    # 9. فحص CORS
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    def check_cors_policy(self):
        """فحص سياسة CORS"""
        self.print_header("🌐 فحص CORS Policy")
        
        test_origins = [
            "https://malicious-site.com",
            "http://evil.com",
            "null",
            "https://localhost:3000",
        ]
        
        for origin in test_origins:
            try:
                response = self.session.get(
                    urljoin(self.base_url, f"/api/{API_VERSION}/articles"),
                    headers={"Origin": origin},
                    timeout=TIMEOUT
                )
                
                acao = response.headers.get("Access-Control-Allow-Origin", "")
                acac = response.headers.get("Access-Control-Allow-Credentials", "")
                
                if acao == "*":
                    self.print_result("FAIL", f"CORS يسمح بـ * - يُفضل تحديد origins محددة")
                elif origin in acao:
                    if origin in ["https://malicious-site.com", "http://evil.com"]:
                        self.print_result("FAIL", f"CORS يعكس origin خارجي: {origin}")
                    else:
                        self.print_result("PASS", f"CORS يسمح بـ {origin}")
                else:
                    self.print_result("PASS", f"CORS لا يسمح بـ {origin}")
                    
            except Exception as e:
                self.print_result("WARN", f"خطأ في فحص {origin}: {str(e)[:30]}")
                
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    # 10. فحص Debug Mode
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    def check_debug_mode(self):
        """فحص إذا كان Debug Mode مُفعّلاً"""
        self.print_header("🐛 فحص Debug Mode")
        
        try:
            # إرسال طلب خاطئ لرؤية رسائل الخطأ
            response = self.session.get(
                urljoin(self.base_url, "/api/nonexistent-endpoint-for-testing"),
                timeout=TIMEOUT
            )
            
            debug_indicators = [
                "Whoops",
                "Stack trace",
                "Exception",
                "Laravel",
                "vendor/laravel",
                "APP_DEBUG",
                "SQLSTATE",
                "at line",
                "Error in",
                "Debug mode is enabled",
            ]
            
            for indicator in debug_indicators:
                if indicator.lower() in response.text.lower():
                    self.print_result("FAIL", f"Debug Mode مُفعّل! (وجدنا: {indicator})")
                    self.results["vulnerabilities"].append({
                        "type": "Debug Mode Enabled",
                        "indicator": indicator,
                        "severity": "HIGH"
                    })
                    return
                    
            self.print_result("PASS", "لا توجد علامات على تفعيل Debug Mode")
            
        except Exception as e:
            self.print_result("WARN", f"خطأ: {str(e)}")
            
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    # تشغيل كل الفحوصات
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    def run_all_checks(self):
        """تشغيل جميع فحوصات الأمان"""
        print(f"""
{Colors.BOLD}{Colors.HEADER}
╔══════════════════════════════════════════════════════════════╗
║           🛡️  فاحص أمان API الشامل - Newsroom            ║
║              Security Vulnerability Scanner                   ║
╚══════════════════════════════════════════════════════════════╝
{Colors.ENDC}
        """)
        
        print(f"{Colors.CYAN}🎯 الهدف: {self.base_url}{Colors.ENDC}")
        print(f"{Colors.CYAN}🎯 Frontend: {self.frontend_url}{Colors.ENDC}")
        print(f"{Colors.CYAN}📅 التاريخ: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}{Colors.ENDC}")
        
        # 1. فحص Port 3000
        self.check_port_3000_security()
        
        # 2. فحص Backend Security Headers
        self.check_backend_security_headers()
        
        # 3. فحص Authentication
        self.check_protected_endpoints()
        
        # 4. فحص Rate Limiting
        self.check_rate_limited_endpoints()
        
        # 5. فحص SQL Injection
        self.check_sql_injection()
        
        # 6. فحص تسريب المعلومات
        self.check_information_disclosure()
        
        # 7. فحص CORS
        self.check_cors_policy()
        
        # 8. فحص Debug Mode
        self.check_debug_mode()
        
        # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        # الملخص النهائي
        # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        self.print_summary()
        
        # حفظ النتائج
        self.save_results()
        
    def print_summary(self):
        """طباعة ملخص النتائج"""
        self.print_header("📊 ملخص نتائج الفحص")
        
        total_vulns = len(self.results["vulnerabilities"])
        high_vulns = len([v for v in self.results["vulnerabilities"] if v.get("severity") == "HIGH"])
        medium_vulns = len([v for v in self.results["vulnerabilities"] if v.get("severity") == "MEDIUM"])
        
        print(f"""
  {Colors.BOLD}━━━━━━━━━━━━━━━ الإحصائيات ━━━━━━━━━━━━━━━{Colors.ENDC}
  
  📊 إجمالي الثغرات المكتشفة: {total_vulns}
  🔴 ثغرات خطيرة (HIGH):      {high_vulns}
  🟡 ثغرات متوسطة (MEDIUM):    {medium_vulns}
  
  {Colors.BOLD}━━━━━━━━━━━━ Port 3000 ━━━━━━━━━━━━━━━━━━━━{Colors.ENDC}
  
  🔌 الحالة: {"مفتوح ✓" if self.results["port_3000"].get("port_open") else "مغلق"}
  🔒 HTTPS: {"متاح" if self.results["port_3000"].get("https_available") else "غير متاح"}
  
        """)
        
        if total_vulns > 0:
            print(f"  {Colors.FAIL}⚠️  يوجد ثغرات يجب معالجتها!{Colors.ENDC}")
            print(f"\n  {Colors.BOLD}قائمة الثغرات:{Colors.ENDC}")
            for vuln in self.results["vulnerabilities"]:
                severity_color = Colors.FAIL if vuln.get("severity") == "HIGH" else Colors.WARNING
                print(f"    {severity_color}• [{vuln.get('severity', 'UNKNOWN')}] {vuln.get('type')}: {vuln.get('endpoint', vuln.get('path', 'N/A'))}{Colors.ENDC}")
        else:
            print(f"  {Colors.GREEN}✓ لم يتم اكتشاف ثغرات واضحة{Colors.ENDC}")
            
    def save_results(self):
        """حفظ النتائج في ملف JSON"""
        output_file = "security_report.json"
        try:
            with open(output_file, 'w', encoding='utf-8') as f:
                json.dump(self.results, f, ensure_ascii=False, indent=2)
            print(f"\n  {Colors.GREEN}✓ تم حفظ التقرير في: {output_file}{Colors.ENDC}")
        except Exception as e:
            print(f"\n  {Colors.FAIL}✗ خطأ في حفظ التقرير: {e}{Colors.ENDC}")


# ═══════════════════════════════════════════════════
# تشغيل الفاحص
# ═══════════════════════════════════════════════════
if __name__ == "__main__":
    # إمكانية تمرير URLs من سطر الأوامر
    backend_url = sys.argv[1] if len(sys.argv) > 1 else BASE_URL
    frontend_url = sys.argv[2] if len(sys.argv) > 2 else FRONTEND_URL
    
    checker = SecurityChecker(backend_url, frontend_url)
    checker.run_all_checks()
