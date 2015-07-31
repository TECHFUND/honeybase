describe("HoneyBase", function() {
  describe("Auth System", function() {
    xdescribe("honeybase.signup()", function() {
      it("should success", function() {
        //expect(true).toBe(true);
      });
    });
    xdescribe("honeybase.signin()", function() {
      it("should success", function() {
      });
    });
    xdescribe("honeybase.logout()", function() {
      it("should success", function() {
      });
    });
    xdescribe("honeybase.auth", function() {
      it("should success", function() {
      });
    });
    xdescribe("honeybase.current_user", function() {
      it("should success", function() {
      });
    });
  });
  describe("Database System", function() {
    var db = honeybase.database("sample");
    xdescribe("db.insert", function() {
      it("should success", function(done) {
      });
    });
    xdescribe("db.update", function() {
      it("should success", function(done) {
      });
    });
    xdescribe("db.select", function() {
      it("should success", function(done) {
        db.select({}).done(function(flag, data){
          // reffererが合わないから落ちた
          console.log(flag, data);
          pending();
          done();
        });
      });
    });
    xdescribe("db.delete", function() {
      it("should success", function(done) {
      });
    });
  });
});
