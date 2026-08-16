from flask import Flask, render_template, request, jsonify, session, redirect, url_for
from flask_cors import CORS
from flask_sqlalchemy import SQLAlchemy
from flask_login import LoginManager, UserMixin, login_user, login_required, logout_user, current_user
from werkzeug.security import generate_password_hash, check_password_hash
from datetime import datetime
import json
import os

app = Flask(__name__)
app.config['SECRET_KEY'] = 'your-secret-key-here'
app.config['SQLALCHEMY_DATABASE_URI'] = 'sqlite:///kersa_school.db'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

CORS(app)
db = SQLAlchemy(app)
login_manager = LoginManager(app)
login_manager.login_view = 'login'

# Database Models
class User(UserMixin, db.Model):
    __tablename__ = 'users'
    
    id = db.Column(db.Integer, primary_key=True)
    full_name = db.Column(db.String(100), nullable=False)
    email = db.Column(db.String(120), unique=True, nullable=False)
    username = db.Column(db.String(50), unique=True, nullable=False)
    password_hash = db.Column(db.String(200), nullable=False)
    role = db.Column(db.String(20), nullable=False, default='student')
    status = db.Column(db.String(20), nullable=False, default='active')
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    
    def set_password(self, password):
        self.password_hash = generate_password_hash(password)
    
    def check_password(self, password):
        return check_password_hash(self.password_hash, password)
    
    def to_dict(self):
        return {
            'id': self.id,
            'full_name': self.full_name,
            'email': self.email,
            'username': self.username,
            'role': self.role,
            'status': self.status
        }

class Student(db.Model):
    __tablename__ = 'students'
    
    id = db.Column(db.Integer, primary_key=True)
    student_id = db.Column(db.String(20), unique=True, nullable=False)
    full_name = db.Column(db.String(100), nullable=False)
    gender = db.Column(db.String(10), nullable=False)
    date_of_birth = db.Column(db.Date, nullable=False)
    grade = db.Column(db.Integer, nullable=False)
    section = db.Column(db.String(10), nullable=False)
    parent_name = db.Column(db.String(100), nullable=False)
    parent_phone = db.Column(db.String(20), nullable=False)
    address = db.Column(db.String(200))
    enrollment_date = db.Column(db.Date, nullable=False)
    status = db.Column(db.String(20), default='active')
    
    def to_dict(self):
        return {
            'id': self.id,
            'student_id': self.student_id,
            'full_name': self.full_name,
            'gender': self.gender,
            'date_of_birth': self.date_of_birth.strftime('%Y-%m-%d'),
            'grade': self.grade,
            'section': self.section,
            'parent_name': self.parent_name,
            'parent_phone': self.parent_phone,
            'address': self.address,
            'status': self.status
        }

class Teacher(db.Model):
    __tablename__ = 'teachers'
    
    id = db.Column(db.Integer, primary_key=True)
    teacher_id = db.Column(db.String(20), unique=True, nullable=False)
    full_name = db.Column(db.String(100), nullable=False)
    qualification = db.Column(db.String(100), nullable=False)
    subject = db.Column(db.String(50), nullable=False)
    phone = db.Column(db.String(20), nullable=False)
    email = db.Column(db.String(120), unique=True, nullable=False)
    hire_date = db.Column(db.Date, nullable=False)
    status = db.Column(db.String(20), default='active')
    
    def to_dict(self):
        return {
            'id': self.id,
            'teacher_id': self.teacher_id,
            'full_name': self.full_name,
            'qualification': self.qualification,
            'subject': self.subject,
            'phone': self.phone,
            'email': self.email,
            'status': self.status
        }

class News(db.Model):
    __tablename__ = 'news'
    
    id = db.Column(db.Integer, primary_key=True)
    title = db.Column(db.String(200), nullable=False)
    content = db.Column(db.Text, nullable=False)
    category = db.Column(db.String(50), nullable=False)
    author = db.Column(db.String(100), nullable=False)
    publish_date = db.Column(db.DateTime, default=datetime.utcnow)
    image_url = db.Column(db.String(200))
    
    def to_dict(self):
        return {
            'id': self.id,
            'title': self.title,
            'content': self.content,
            'category': self.category,
            'author': self.author,
            'publish_date': self.publish_date.strftime('%Y-%m-%d %H:%M:%S'),
            'image_url': self.image_url
        }

class Admission(db.Model):
    __tablename__ = 'admissions'
    
    id = db.Column(db.Integer, primary_key=True)
    application_no = db.Column(db.String(20), unique=True, nullable=False)
    first_name = db.Column(db.String(50), nullable=False)
    middle_name = db.Column(db.String(50))
    last_name = db.Column(db.String(50), nullable=False)
    gender = db.Column(db.String(10), nullable=False)
    date_of_birth = db.Column(db.Date, nullable=False)
    applying_for_grade = db.Column(db.Integer, nullable=False)
    parent_name = db.Column(db.String(100), nullable=False)
    relationship = db.Column(db.String(50), nullable=False)
    phone = db.Column(db.String(20), nullable=False)
    email = db.Column(db.String(120))
    previous_school = db.Column(db.String(200), nullable=False)
    previous_grade = db.Column(db.String(20), nullable=False)
    additional_info = db.Column(db.Text)
    application_date = db.Column(db.DateTime, default=datetime.utcnow)
    status = db.Column(db.String(20), default='pending')
    
    def to_dict(self):
        return {
            'id': self.id,
            'application_no': self.application_no,
            'first_name': self.first_name,
            'middle_name': self.middle_name,
            'last_name': self.last_name,
            'gender': self.gender,
            'date_of_birth': self.date_of_birth.strftime('%Y-%m-%d'),
            'applying_for_grade': self.applying_for_grade,
            'parent_name': self.parent_name,
            'relationship': self.relationship,
            'phone': self.phone,
            'email': self.email,
            'previous_school': self.previous_school,
            'previous_grade': self.previous_grade,
            'additional_info': self.additional_info,
            'application_date': self.application_date.strftime('%Y-%m-%d %H:%M:%S'),
            'status': self.status
        }

@login_manager.user_loader
def load_user(user_id):
    return User.query.get(int(user_id))

# Routes
@app.route('/')
def index():
    return render_template('index.html')

@app.route('/api/students')
def get_students():
    students = Student.query.all()
    return jsonify({
        'success': True,
        'students': [student.to_dict() for student in students]
    })

@app.route('/api/students/<int:id>')
def get_student(id):
    student = Student.query.get_or_404(id)
    return jsonify({
        'success': True,
        'student': student.to_dict()
    })

@app.route('/api/students', methods=['POST'])
@login_required
def create_student():
    data = request.json
    
    # Generate student ID
    year = datetime.now().year
    count = Student.query.count() + 1
    student_id = f'STU{year}{str(count).zfill(4)}'
    
    student = Student(
        student_id=student_id,
        full_name=data['full_name'],
        gender=data['gender'],
        date_of_birth=datetime.strptime(data['date_of_birth'], '%Y-%m-%d').date(),
        grade=data['grade'],
        section=data['section'],
        parent_name=data['parent_name'],
        parent_phone=data['parent_phone'],
        address=data.get('address', ''),
        enrollment_date=datetime.now().date(),
        status='active'
    )
    
    db.session.add(student)
    db.session.commit()
    
    return jsonify({
        'success': True,
        'message': 'Student created successfully',
        'student': student.to_dict()
    }), 201

@app.route('/api/teachers')
def get_teachers():
    teachers = Teacher.query.all()
    return jsonify({
        'success': True,
        'teachers': [teacher.to_dict() for teacher in teachers]
    })

@app.route('/api/news')
def get_news():
    news = News.query.order_by(News.publish_date.desc()).all()
    return jsonify({
        'success': True,
        'news': [item.to_dict() for item in news]
    })

@app.route('/api/news', methods=['POST'])
@login_required
def create_news():
    data = request.json
    
    news = News(
        title=data['title'],
        content=data['content'],
        category=data['category'],
        author=current_user.full_name,
        image_url=data.get('image_url', '')
    )
    
    db.session.add(news)
    db.session.commit()
    
    return jsonify({
        'success': True,
        'message': 'News created successfully',
        'news': news.to_dict()
    }), 201

@app.route('/api/admissions', methods=['POST'])
def submit_admission():
    data = request.json
    
    # Generate application number
    year = datetime.now().year
    count = Admission.query.count() + 1
    application_no = f'APP{year}{str(count).zfill(4)}'
    
    admission = Admission(
        application_no=application_no,
        first_name=data['first_name'],
        middle_name=data.get('middle_name', ''),
        last_name=data['last_name'],
        gender=data['gender'],
        date_of_birth=datetime.strptime(data['date_of_birth'], '%Y-%m-%d').date(),
        applying_for_grade=data['applying_for_grade'],
        parent_name=data['parent_name'],
        relationship=data['relationship'],
        phone=data['phone'],
        email=data.get('email', ''),
        previous_school=data['previous_school'],
        previous_grade=data['previous_grade'],
        additional_info=data.get('additional_info', ''),
        status='pending'
    )
    
    db.session.add(admission)
    db.session.commit()
    
    return jsonify({
        'success': True,
        'message': 'Application submitted successfully',
        'application_no': application_no,
        'admission': admission.to_dict()
    }), 201

@app.route('/api/login', methods=['POST'])
def login():
    data = request.json
    
    user = User.query.filter_by(username=data['username']).first()
    
    if user and user.check_password(data['password']):
        if user.status == 'active':
            login_user(user)
            return jsonify({
                'success': True,
                'message': 'Login successful',
                'user': user.to_dict()
            })
        else:
            return jsonify({
                'success': False,
                'message': 'Account is inactive'
            }), 403
    else:
        return jsonify({
            'success': False,
            'message': 'Invalid username or password'
        }), 401

@app.route('/api/register', methods=['POST'])
def register():
    data = request.json
    
    # Check if user already exists
    if User.query.filter_by(username=data['username']).first():
        return jsonify({
            'success': False,
            'message': 'Username already exists'
        }), 400
    
    if User.query.filter_by(email=data['email']).first():
        return jsonify({
            'success': False,
            'message': 'Email already exists'
        }), 400
    
    user = User(
        full_name=data['full_name'],
        email=data['email'],
        username=data['username'],
        role=data.get('role', 'student'),
        status='active'
    )
    user.set_password(data['password'])
    
    db.session.add(user)
    db.session.commit()
    
    return jsonify({
        'success': True,
        'message': 'Registration successful',
        'user': user.to_dict()
    }), 201

@app.route('/api/logout')
@login_required
def logout():
    logout_user()
    return jsonify({
        'success': True,
        'message': 'Logout successful'
    })

@app.route('/api/dashboard')
@login_required
def dashboard():
    stats = {
        'total_students': Student.query.count(),
        'total_teachers': Teacher.query.count(),
        'total_news': News.query.count(),
        'total_admissions': Admission.query.count()
    }
    
    return jsonify({
        'success': True,
        'stats': stats,
        'user': current_user.to_dict()
    })

if __name__ == '__main__':
    with app.app_context():
        db.create_all()
    app.run(debug=True, port=5000)
