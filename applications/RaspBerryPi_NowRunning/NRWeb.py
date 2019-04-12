import socketserver
# for python2
# from BaseHTTPServer import BaseHTTPRequestHandler
# for python3
import time
import threading
import cgi
import json
from os import curdir,sep
from http.server import BaseHTTPRequestHandler

import NRDisplay
import NRNetwork
import NROptions

class MyHandler(BaseHTTPRequestHandler):

	def readData(self):
		print("read data")
		# properly handle
		# evaluate mode
		if self.server.displayHandler.getCountDown() != 0:
			modo="Reconocimiento"
		elif self.server.displayHandler.getClockMode() == True:
			modo="Reloj"
		else:
			modo="Turno"
		# pending: retrieve all required parameters
		data = {
			'Ring': self.server.displayHandler.getRing(),
			'Round': self.server.displayHandler.getRoundInfo(),
			'Categoria': self.server.displayHandler.getCategoria(),
			'Grado': self.server.displayHandler.getGrado(),
			'Reconocimiento': int(self.server.menuHandler.getCountDown()/60),
			'Brillo': self.server.displayHandler.getBrightness(),
			'Numero': self.server.displayHandler.getNowRunning(),
			'ServerIP': self.server.networkHandler.getServerAddress(),
			'HostIP': self.server.networkHandler.getHostAddress(),
			'Mode': modo,
			'Success': True
		}
		return json.dumps(data,separators=(',', ':'))

	def writeData(self,form):
		print("Write_data")
		# pending: set all received parameters
		for item in form:
			val=form[item].value
			print("%s >>> %s" %(item,val) )
			if item == 'Ring':
				self.server.displayHandler.setRing(int(val))
			elif item == 'Round':
				self.manga=val
			elif item == "Categoria":
				self.cat=val
			elif item == "Grado":
				self.grad=val
			elif item == "Reconocimiento":
				self.server.menuHandler.setDirectCountDown(int(val))
			elif item == "Brillo":
				self.server.displayHandler.setBrightness(int(val))
			elif item == "Numero":
				self.server.displayHandler.setNowRunning(int(val))
			elif item == 'Mode':
				if val == 'Reloj':
					self.server.displayHandler.setCountDown(0)
					self.server.displayHandler.setClockMode(True)
				elif val == "Reconocimiento":
					self.server.displayHandler.setClockMode(False)
					self.server.displayHandler.setCountDown(self.server.menuHandler.getCountDown())
				else:
					self.server.displayHandler.setCountDown(0)
					self.server.displayHandler.setClockMode(False)
			elif item == 'Stop':
			    if int(val) == 1:
				    self.server.displayHandler.setCountDown(0)
			else:
				print("Unhandled Key: %s Value:%s" %(item,form[item].value) )

		str= "%s %s - %s" %(self.manga,self.cat,self.grad)
		self.server.displayHandler.setRoundInfo(self.manga,self.cat,self.grad)

	def do_GET(self):
		if self.path=="/":
			self.path="/index.html"

		try:
			#Check the file extension required and
			#set the right mime type

			sendFile = False
			str=""
			mimetype='text/html'
			if self.path.startswith( '/readData'):
				mimetype='application/json'
				str=self.readData()
			if self.path.endswith(".html"):
				mimetype='text/html'
				sendFile = True
			if self.path.endswith(".jpg"):
				mimetype='image/jpg'
				sendFile = True
			if self.path.endswith(".gif"):
				mimetype='image/gif'
				sendFile = True
			if self.path.endswith(".png"):
				mimetype='image/png'
				sendFile = True
			if self.path.endswith(".js"):
				mimetype='application/javascript'
				sendFile = True
			if self.path.endswith(".css"):
				mimetype='text/css'
				sendFile = True

			# try to read file
			data =str.encode()
			if sendFile == True:
				#Open the static file requested and send it
				f = open(curdir + sep + self.path,'rb')
				data=f.read()
				f.close()

			# prepare headers
			self.send_response(200)
			self.send_header('Content-type',mimetype)
			self.end_headers()
			self.wfile.write(data)
			return

		except IOError:
			self.send_error(404,'File Not Found: %s' % self.path)

	def do_POST(self):
		if self.path=="/writeData":
			form = cgi.FieldStorage(
				fp=self.rfile,
				headers=self.headers,
				environ={'REQUEST_METHOD':'POST',
						 'CONTENT_TYPE':self.headers['Content-Type'],
			})
			self.writeData(form)
			data=self.readData()
			self.send_response(200)
			self.send_header('Content-type','application/json')
			self.end_headers()
			self.wfile.write(data.encode())
			return

	def do_PUT(self):
		self.do_POST()

class NRWeb:
	loop = True

	def stopWeb(self):
		NRWeb.loop=False

	def webLoop(self):
		server_thread = threading.Thread(target=self.httpd.serve_forever)
		# Exit the server thread when the main thread terminates
		server_thread.daemon = True
		server_thread.start()
		# as using threads for clients, main thread only monitorizes end of loop, so just sleep and wait
		while NRWeb.loop == True:
			time.sleep(5) # check for end every 5 seconds,
		self.httpd.shutdown()
		self.httpd.server_close()

		# while NRWen.loop == True

	def __init__(self,port,dsp,net,menu):
		self.httpd = socketserver.TCPServer(("", port), MyHandler)
		# ad dsp,net,and menu handler as extra variables
		self.httpd.displayHandler=dsp
		self.httpd.networkHandler=net
		self.httpd.menuHandler=menu
		self.httpd.server_name = "localhost"
		self.httpd.server_port = port
